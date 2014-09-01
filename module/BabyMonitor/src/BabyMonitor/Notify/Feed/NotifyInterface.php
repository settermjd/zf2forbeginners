<?php
/**
 * Created by PhpStorm.
 * User: mattsetter
 * Date: 31/08/14
 * Time: 21:04
 */

namespace BabyMonitor\Notify\Feed;

use BabyMonitor\Model\FeedModel;

interface NotifyInterface
{
    public function notify(
        FeedModel $feed, $notificationType
    );
}