<?php
/*
 * This file is part of the codeliner/php-service-bus.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 11.03.14 - 22:14
 */

namespace Codeliner\ServiceBus\Event;

use Codeliner\ServiceBus\Exception\RuntimeException;
use Codeliner\ServiceBus\Service\Definition;
use Codeliner\ServiceBus\Service\EventReceiverManager;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DefaultEventReceiverFactory
 *
 * @package Codeliner\ServiceBus\Event
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class DefaultEventReceiverFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param string                  $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $serviceLocator instanceof EventReceiverManager;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param string                  $requestedName
     * @throws \Codeliner\ServiceBus\Exception\RuntimeException
     * @return EventReceiverInterface
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!$serviceLocator instanceof EventReceiverManager) {
            throw new RuntimeException(
                sprintf(
                    "%s is used in the wrong context. It can only be used within a'
                     . ' Codeliner\ServiceBus\Service\EventReceiverManager",
                    get_class($this)
                )
            );
        }

        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $configuration = $mainServiceLocator->get('configuration');

        if (!isset($configuration[Definition::CONFIG_ROOT])) {
            throw new RuntimeException(
                sprintf(
                    'Config root %s is missing in global configuration',
                    Definition::CONFIG_ROOT
                )
            );
        }

        $configuration = $configuration[Definition::CONFIG_ROOT];

        if (!isset($configuration[Definition::EVENT_BUS])) {
            throw new RuntimeException(
                sprintf(
                    'event_bus config is missing in %s configuration',
                    Definition::CONFIG_ROOT
                )
            );
        }

        $configuration = $configuration[Definition::EVENT_BUS];

        if (! isset($configuration[$requestedName])) {
            throw new RuntimeException(
                sprintf(
                    'Configuration for %s bus is missing in %s.%s configuration',
                    $requestedName,
                    Definition::CONFIG_ROOT,
                    Definition::EVENT_BUS
                )
            );
        }

        $configuration = $configuration[$requestedName];

        if (!isset($configuration[Definition::EVENT_MAP])) {
            throw new RuntimeException(
                sprintf(
                    '%s Configuration for %s bus is missing in %s.%s configuration',
                    Definition::EVENT_MAP,
                    $requestedName,
                    Definition::CONFIG_ROOT,
                    Definition::EVENT_BUS
                )
            );
        }

        $eventReceiver = new EventReceiver($configuration[Definition::EVENT_MAP], $mainServiceLocator);

        $configuration = $mainServiceLocator->get('configuration');

        if (isset($configuration[Definition::CONFIG_ROOT][Definition::EVENT_HANDLER_INVOKE_STRATEGIES])) {
            $eventReceiver->setInvokeStrategies(
                $configuration[Definition::CONFIG_ROOT][Definition::EVENT_HANDLER_INVOKE_STRATEGIES]
            );
        }

        if ($mainServiceLocator->has(Definition::INVOKE_STRATEGY_MANAGER)) {
            $eventReceiver->setInvokeStrategyManager(
                $mainServiceLocator->get(Definition::INVOKE_STRATEGY_MANAGER)
            );
        }

        if ($mainServiceLocator->has(Definition::EVENT_FACTORY)) {
            $eventReceiver->setEventFactory($mainServiceLocator->get(Definition::EVENT_FACTORY));
        }

        return $eventReceiver;
    }
}
 