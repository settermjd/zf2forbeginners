<?php
/**
 * Created by PhpStorm.
 * User: mattsetter
 * Date: 29/08/14
 * Time: 20:13
 */

namespace BabyMonitor\Tables;

use BabyMonitor\Model\FeedModel;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where as WherePredicate;
use Zend\Stdlib\ArrayObject;

class FeedTable
{
    const DATETIME_FORMAT = 'd-m-Y';

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function save(FeedModel $feed)
    {
        $data = array(
            'feedDate' => $feed->feedDate,
            'feedTime' => $feed->feedTime,
            'feedAmount' => $feed->feedAmount,
            'feedNotes' => $feed->feedNotes,
            'feedTemperature' => $feed->feedTemperature,
        );
        $feedId = (int)$feed->feedId;
        if ($feedId == 0) {
            if ($this->tableGateway->insert($data)) {
                return $this->tableGateway->getLastInsertValue();
            }
        } else {
            $retstat = $this->tableGateway->update(
                $data, array('feedId' => $feedId)
            );
            if ($retstat) {
                return $retstat;
            }
        }
    }

    public function delete($feedId)
    {
        if (!empty($feedId)) {
            return $this->tableGateway->delete(array(
                    "feedId" => (int)$feedId
                ));
        }

        return false;
    }

    public function fetchById($feedId)
    {
        if (!empty($feedId)) {
            $select = $this->tableGateway
                ->getSql()
                ->select();
            $select->where(
                array(
                    "feedId" => (int)$feedId
                )
            );
            $results = $this->tableGateway
                ->selectWith($select);

            if ($results->count() == 1) {
                return $results->current();
            }
        }

        return false;
    }

    public function fetchMostRecentFeeds($limit = 5)
    {
        if (!empty($limit)) {
            $select = $this->tableGateway->getSql()->select();
            $select->limit((int)$limit)
                ->order('feedDate DESC, feedTime DESC');
            $results = $this->tableGateway->selectWith($select);

            return $results;
        }
        return false;
    }

    public function fetchByDateRange(\DateTime $startDate = null, \DateTime $endDate = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $where = new WherePredicate();
        $whereClause = array();

        if (!is_null($startDate)) {
            $whereClause[] = $where->greaterThanOrEqualTo(
                'feedDate', $startDate->format(self::DATETIME_FORMAT)
            );
        }

        if (!is_null($endDate)) {
            $whereClause[] = $where->lessThanOrEqualTo(
                'feedDate', $endDate->format(self::DATETIME_FORMAT)
            );
        }

        $select->where($whereClause)->order(
            "feedDate DESC, feedTime DESC"
        );

        $results = $this->tableGateway->selectWith($select);

        return $results;
    }
} 