JVBase 1.0
================
Create By: Jaime Marcelo Valasek

Module base to use a structure Mapper Service and the ZF2

Start program with a structure ready. Advisable to use the Module JVBase for beginners and / or people who have no knowledge of how to build a well structured project.

Installation
-----
Download this module into the vendor folder of your project.

Enable the module in the file application.config.php. Add the module JVBase.

Add this code inside your Module.php APPLICATION

```php
public function getServiceManager() 
    {
    	return array(
    		'initializers' => array(
    			function ($instance, $sm) {
    				if ($instance instanceof \JVBase\Adapter\DbAdapterAwareInterface) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$instance->setDbAdapter($dbAdapter);
    				}
    			}
    		)
    	);
    }
```

Usage tutorials
-----
http://www.youtube.com/zf2tutoriais

http://www.zf2.com.br/tutoriais