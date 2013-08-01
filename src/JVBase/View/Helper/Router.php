<?php

namespace JVBase\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Router extends AbstractHelper
{
	protected $router;
	
	public function __construct($router)
	{
	    $this->router = $router;
	}
    
    public function __invoke()
    {
        return array(
            'router' => $this->router,
        );
    }
}