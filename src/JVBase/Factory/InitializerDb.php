<?php

namespace JVBase\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

class InitializerDb implements InitializerInterface
{
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if($instance instanceof \JVBase\Adapter\DbAdapterAwareInterface){
			$dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
			$instance->setDbAdapter($dbAdapter);
		}
    }
}