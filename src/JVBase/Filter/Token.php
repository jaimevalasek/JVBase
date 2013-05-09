<?php

namespace JVBase\Filter;

class Token
{
	public function microtimeToken()
	{
		$microtime = microtime();
		$microtime = str_replace('.', '', $microtime);
		$microtime = explode(' ', $microtime);
		$microtime = $microtime[1] . $microtime[0];
		$microtime = substr($microtime, 0, 17);
		
		return $microtime;
	} 
}