<?php

namespace JVBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

class Module implements AutoloaderProviderInterface
{
	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
				)
			)
		);
	}
	
	/* public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	} */
	
	public function getServiceConfig()
	{
		return array(
			'invokables' => array(
				'jvbase_service_ready' => 'JVBase\Service\Ready',
				'jvbase_mapper_ready' => 'JVBase\Mapper\Ready',
				
				'jvbase_filter_token' => 'JVBase\Filter\Token',
				'jvbase_filter_basedate' => 'JVBase\Filter\Date',
				'jvbase_filter_string' => 'JVBase\Filter\String',
			),
			'initializers' => array(
				function($instance, $sm){
					if($instance instanceof \BASEDefault\Adapter\DbAdapterAwareInterface){
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$instance->setDbAdapter($dbAdapter);
					}
				},
			),
		);
	}
}