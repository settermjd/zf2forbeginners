<?php
/**
 * Created by PhpStorm.
 * User: mattsetter
 * Date: 29/08/14
 * Time: 20:45
 */

namespace BabyMonitor\Tables\Factories;

use BabyMonitor\Tables\FeedTable;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FeedTableFactory implements FactoryInterface
{
    public function createService(
        ServiceLocatorInterface $serviceLocator
    ) {
        $tableGateway = $serviceLocator->get(
            'BabyMonitor\Tables\FeedTableGateway'
        );
        $table = new FeedTable($tableGateway);

        return $table;
    }
}