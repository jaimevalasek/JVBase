<?php

namespace JVBase\Filter;

class Date
{
	public function dbNow()
	{
		return \date('Y-m-d H:i:s');
	} 

	public function brNow()
	{
		return \date('d-m-Y H:i:s');
	}
	
	public function convert($date)
	{
		return \date('d/m/Y H:i:s', strtotime($date));
	}
}