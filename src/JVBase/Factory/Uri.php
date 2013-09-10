<?php

namespace JVBase\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class Uri implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new \JVBase\View\Helper\Uri($serviceLocator->getServiceLocator()->get('application')->getRequest()->getQuery()->toArray());
    }
}