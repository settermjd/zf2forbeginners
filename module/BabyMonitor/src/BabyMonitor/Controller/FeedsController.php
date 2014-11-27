<?php

/**
 * The Feeds Controller
 *
 * @category   BabyMonitor
 * @package    Controller
 * @author     Matthew Setter <matthew@maltblue.com>
 * @copyright  2014 Matthew Setter <matthew@maltblue.com>
 * @since      File available since Release/Tag: 1.0
 */

namespace BabyMonitor\Controller;

use BabyMonitor\Model\FeedModel;
use BabyMonitor\Tables\FeedTable;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Adapter\Iterator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class FeedsController extends AbstractActionController
{
    /**
     * @var int default amount of feeds to display, max, per/page
     */
    const DEFAULT_FEED_COUNT = 5;

    /**
     * @var string The default route, used to avoid magic variables
     */
    const DEFAULT_ROUTE = 'feeds';

    /**
     * @var int
     */
    const DEFAULT_RECORDS_PER_PAGE = 20;

    /**
     * @var int
     */
    const DEFAULT_PAGE = 1;

    /**
     * default cache key for recent feeds
     */
    const KEY_ALL_RESULTS = "recent_feeds";


    protected $appConfig;

    /**
     * @var FeedTable
     */
    protected $feedTable;

    /**
     * @var \Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected $cache;

    public function __construct(FeedTable $feedTable, $appConfig = null, $cache = null)
    {
        $this->feedTable = $feedTable;

        if (!is_null($appConfig)) {
            $this->appConfig = $appConfig;
        }

        if (!is_null($cache)) {
            $this->cache = $cache;
        }
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach(
            'dispatch',
            function ($e) use ($controller) {
                $controller->layout(
                    'layout/index-action-layout'
                );
            },
            100
        );
    }

    public function indexAction()
    {
        if (!is_null($this->cache)) {
            if (!$this->cache->hasItem(self::KEY_ALL_RESULTS)) {
                $resultset = $this->feedTable->fetchMostRecentFeeds(
                    self::DEFAULT_FEED_COUNT
                );
                $this->cache->setItem(self::KEY_ALL_RESULTS, $resultset->toArray());
            } else {
                $resultset = $this->cache->getItem(
                    self::KEY_ALL_RESULTS
                );
            }
        } else {
            $resultset = $this->feedTable->fetchMostRecentFeeds(
                self::DEFAULT_FEED_COUNT
            );
        }

        return new ViewModel(
            array(
                'paginator' => $this->getPaginator($resultset)
            )
        );
    }

    public function searchAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $configData = array(
            'application_name' => $config['app']['name'],
            'webmaster_name' => $config['app']['webmaster']['name'],
            'webmaster_email' => $config['app']['webmaster']['email'],
        );

        $formManager = $this->serviceLocator->get(
            'FormElementManager'
        );

        $form = $formManager->get(
            'BabyMonitor\Forms\SearchForm'
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                return $this->redirect()->toRoute(
                    'feeds/search',
                    array(
                        'startDate' => $form->getInputFilter()->getValue('startDate'),
                        'endDate' => $form->getInputFilter()->getValue('endDate'),
                    )
                );
            }
        }

        if ($this->getRequest()->isGet()) {
            $form->setData($this->params()->fromRoute());

            if ($form->isValid()) {

                $startDate = null;
                if (!is_null($form->getInputFilter()->getValue('startDate', null))) {
                    $startDate = new \DateTime(
                        $form->getInputFilter()->getValue('startDate', null)
                    );
                }

                $endDate = null;
                if (!is_null($form->getInputFilter()->getValue('endDate', null))) {
                    $endDate = new \DateTime(
                        $form->getInputFilter()->getValue('endDate', null)
                    );
                }

                $paginator = $this->getPaginator(
                    $this->feedTable->fetchByDateRange($startDate, $endDate)
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'paginator' => (isset($paginator)) ? $paginator : new Paginator(
                    new ArrayAdapter(array())
                ),
            )
        );
    }

    protected function getPaginator($resultset)
    {
        if (is_array($resultset)) {
            $paginator = new Paginator(
                new ArrayAdapter($resultset)
            );
        } else {
            $paginator = new Paginator(
                new Iterator($resultset)
            );
        }

        $paginator->setCurrentPageNumber(
            $this->params()->fromRoute(
                'page',
                self::DEFAULT_PAGE
            )
        );

        $paginator->setItemCountPerPage(
            $this->params()->fromRoute(
                'perPage',
                self::DEFAULT_RECORDS_PER_PAGE
            )
        );

        return $paginator;
    }

    public function deleteAction()
    {
        $formManager = $this->serviceLocator->get('FormElementManager');
        $form = $formManager->get('BabyMonitor\Forms\DeleteForm');

        if ($this->getRequest()->isGet()) {
            $form->setData(
                array(
                    'feedId' => (int)$this->params()->fromRoute('id')
                )
            );
            if ($form->isValid()) {
                $feedTableExists = $this->feedTable->fetchById(
                    $form->getInputFilter()->getValue('feedId')
                );
                if (!$feedTableExists) {
                    $this->flashMessenger()->addErrorMessage(
                        "Unable to find that feed. Perhaps you meant a different one?"
                    );
                    return $this->redirect()->toRoute('feeds', array());
                }
            }
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                if ($this->feedTable->delete($form->getInputFilter()->getValue('feedId'))) {
                    $feed = new FeedModel();
                    $feed->exchangeArray($form->getData());
                    $this->flashMessenger()->addInfoMessage("Feed Deleted.");
                    return $this->redirect()->toRoute('feeds', array());
                }
            }
        }

        return new ViewModel(
            array('form' => $form,)
        );
    }

    public function manageAction()
    {
        $formManager = $this->serviceLocator->get(
            'FormElementManager'
        );

        $form = $formManager->get(
            'BabyMonitor\Forms\ManageRecordForm'
        );

        $feedId = (int)$this->params()->fromRoute('id');

        if ($this->getRequest()->isGet()) {
            if (!empty($feedId)) {
                if ($feed = $this->feedTable->fetchById($feedId)) {
                    $form->setData($feed->getArrayCopy());
                } else {
                    return $this->redirect()->toRoute(
                        self::DEFAULT_ROUTE,
                        array('action' => 'manage')
                    );
                }
            }
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $feed = new FeedModel();
                $feed->exchangeArray($form->getData());
                $feed->feedId = $this->feedTable->save($feed);
                $this->getEventManager()->trigger(
                    'Feed.Create',
                    $this,
                    array(
                        'feedData' => $feed
                    )
                );

                return $this->redirect()->toRoute(self::DEFAULT_ROUTE, array());
            }
        }

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }

    public function viewAction()
    {
        $view = new ViewModel();
        $view->setVariables(
            array(
                'currentDate' => new \DateTime(),
                'fullName' => 'Matthew Setter'
            )
        );
        $view->setTemplate(
            'BabyMonitor\Feeds\mytemplate'
        );
        return $view;
    }

}

