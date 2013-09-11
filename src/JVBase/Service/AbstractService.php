<?php

namespace JVBase\Service;

use Zend\Paginator\Adapter\ArrayAdapter;

use Zend\Paginator\Paginator;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface;

abstract class AbstractService implements ServiceLocatorAwareInterface
{
	protected $serviceLocator;
	protected $entityMapper;
	protected $itemCountPerPage;
	protected $currentPageNumber;
	protected $pageFilter; // adicionar o esquema do filtro
	
	/********************************* TRANSATIONS **********************************/
	public function insert($data, $table = null, $returnEntity = false)
	{
		$result = $this->getEntityMapper()->insert($data, $table, $returnEntity);
		return $result;
	}
	
	public function update($data, $where, $table = null, $returnEntity = false)
	{
		$result = $this->getEntityMapper()->update($data, $where, $table, $returnEntity);
		return $result;
	}
	
	public function delete($where, $table = null)
	{
		$result = $this->getEntityMapper()->delete($where, $table);
		return $result;
	}
	
	/************************************ OPTIONS ************************************/
	public function usePaginator(array $paginatorOptions = array())
	{
	    $this->setItemCountPerPage($paginatorOptions['itemCountPerPage']);
	    $this->setCurrentPageNumber($paginatorOptions['currentPageNumber']);
		$this->getEntityMapper()->usePaginator($paginatorOptions);
	}
	
	public function paginatorArray($data)
	{
	    $paginator = new Paginator(new ArrayAdapter($data));
	    $paginator->setItemCountPerPage($this->getItemCountPerPage());
	    $paginator->setCurrentPageNumber($this->getCurrentPageNumber());
	    
	    return $paginator;
	}
	
	public function setItemCountPerPage($itemCountPerPage)
	{
	    $this->itemCountPerPage = $itemCountPerPage;
	}
	
	public function getItemCountPerPage()
	{
	    return $this->itemCountPerPage;
	}
	
	public function setCurrentPageNumber($currentPageNumber)
	{
	    $this->currentPageNumber = $currentPageNumber;
	}
	
	public function getCurrentPageNumber()
	{
	    return $this->currentPageNumber;
	}
	
	/************************************ SELECTS ************************************/
	public function findAll($table = null, $resultType = 'array', $order = null, $limit = null)
	{
		$result = $this->getEntityMapper()->findAll($table, $resultType, $order, $limit);
		return $result;
	}
	
	public function findAllBy(array $where, $table = null, $resultType = 'array', $order = null, $limit = null) {
		$result = $this->getEntityMapper()->findAllBy($where, $table, $resultType, $order, $limit);
		return $result;
	}
	
	public function findById($id, $table = null, $resultType = 'array')
	{
		$result = $this->getEntityMapper()->findById($id, $table, $resultType);
		return $result;
	}
	
	public function findOneBy(array $where, $table = null, $resultType = 'array')
	{
		$result = $this->getEntityMapper()->findOneBy($where, $table, $resultType);
		return $result;
	}
	
	/******************************** GETS AND SETERS ********************************/
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
		return $this;
	}
	
	public function getEntityMapper()
	{
		if (is_string($this->entityMapper) && strstr($this->entityMapper, '_mapper_')) {
			$this->entityMapper = $this->getServiceLocator()->get($this->entityMapper);
		}
		
		return $this->entityMapper;
	}
	
	public function setEntityMapper($entityMapper)
	{
		$this->entityMapper = $entityMapper;
		return $this;
	}
}