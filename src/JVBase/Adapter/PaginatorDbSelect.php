<?php

namespace JVBase\Adapter;

use Zend\Paginator\Adapter\DbSelect;

class PaginatorDbSelect extends DbSelect
{
	public function getItems($offset, $itemCountPerPage) {
		$items = parent::getItems($offset, $itemCountPerPage);
		$return = array();
		
		foreach ($items as $item) {
			$return[] =  $item;
		}
		
		return $return;
	}
}