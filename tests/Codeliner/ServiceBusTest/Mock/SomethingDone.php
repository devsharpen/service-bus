<?php
/*
 * This file is part of the codeliner/php-service-bus.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 11.03.14 - 21:17
 */

namespace Codeliner\ServiceBusTest\Mock;

use Codeliner\ServiceBus\Event\AbstractEvent;

/**
 * Class SomethingDone
 *
 * @package Codeliner\ServiceBusTest\Mock
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class SomethingDone extends AbstractEvent
{
    /**
     * @param string $dataString
     * @return SomethingDone
     */
    public static function fromData($dataString)
    {
        return new self(array('data' => $dataString));
    }
    /**
     * @return string
     */
    public function data()
    {
        return $this->payload['data'];
    }
}
 