<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class allows for sending email notifications when a feed record's created, updated or deleted.
 *
 * @category   BabyMonitor
 * @package    Notify
 * @author     Matthew Setter <matthew@maltblue.com>
 * @copyright  2014 Matthew Setter <matthew@maltblue.com>
 * @since      File available since Release/Tag: 1.0
 */
namespace BabyMonitor\Cache;

use Zend\Cache\Exception\ExtensionNotLoadedException;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $cache = null;

        if (array_key_exists('cache', $config)) {
            try {
                $cache = StorageFactory::factory($config['cache']);
            } catch (ServiceNotCreatedException $e) {
                try {
                    $serviceLocator->get('EnliteMonologService')
                                   ->addError($e->getMessage());
                } catch (\UnexpectedValueException $e) {
                    // unable to handle exception
                    // ...
                }
            } catch (ExtensionNotLoadedException $e) {
                try {
                    $serviceLocator->get('EnliteMonologService')->addError($e->getMessage());
                } catch (\UnexpectedValueException $e) {
                    // unable to handle exception
                }
            } catch (\Exception $e) {
                try {
                    $serviceLocator->get('EnliteMonologService')->addError($e->getMessage());
                } catch (\UnexpectedValueException $e) {
                    // unable to handle exception
                }
            }
        }

        if (is_null($cache)) {
            $cache = StorageFactory::factory(
                array(
                    'adapter' => array(
                        'name' => 'memory',
                    ),
                )
            );
        }

        return $cache;
    }
} 