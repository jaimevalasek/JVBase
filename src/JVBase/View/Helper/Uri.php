<?php

namespace JVBase\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Uri extends AbstractHelper
{
	protected $uri;
	
	public function __construct($uri)
	{
	    $this->uri = $uri;
	}
    
    public function __invoke()
    {
        return array(
            'uri' => $this->uri,
        );
    }
}