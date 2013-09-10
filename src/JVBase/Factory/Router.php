<?php

namespace JVBase\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class Router implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new \JVBase\View\Helper\Router($serviceLocator->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch());
    }
}