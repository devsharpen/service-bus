<?php
/*
 * This file is part of the codeliner/php-service-bus.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 08.03.14 - 22:44
 */

namespace Codeliner\ServiceBus\InvokeStrategy;

use Codeliner\ServiceBus\Command\CommandInterface;

/**
 * Class CallbackStrategy
 *
 * @package Codeliner\ServiceBus\InvokeStrategy
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class CallbackStrategy implements InvokeStrategyInterface
{

    /**
     * @param mixed                           $aHandler
     * @param CommandInterface|EventInterface $aCommandOrEvent
     * @return bool
     */
    public function canInvoke($aHandler, $aCommandOrEvent)
    {
        return is_callable($aHandler);
    }

    /**
     * @param mixed                           $aHandler
     * @param CommandInterface|EventInterface $aCommandOrEvent
     */
    public function invoke($aHandler, $aCommandOrEvent)
    {
        call_user_func($aHandler, $aCommandOrEvent);
    }
}
