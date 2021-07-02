<?php

namespace Pckg\Framework\Test;

use Pckg\Queue\Service\Driver\Mock;

trait Queues
{

    public function _beforeQueuesExtension()
    {
        queue()->setDriver(new Mock());
    }

    public function _afterQueuesExtension()
    {
    }
}
