<?php

namespace JVBase\Mapper;

use Zend\Paginator\Paginator;

use Zend\Db\Adapter\Adapter,
	Zend\Db\ResultSet\ResultSet,
	Zend\Db\Sql\Select,
	Zend\Db\Sql\Sql,
	Zend\Db\ResultSet\HydratingResultSet,
	Zend\Db\ResultSet\ResultSetInterface;

use Zend\Stdlib\Hydrator\ClassMethods;

use JVBase\Adapter\PaginatorDbSelect;
use JVBase\Adapter\DbAdapterAwareInterface;

class AbstractMapper implements DbAdapterAwareInterface
{
	protected $model;
	protected $table;
	protected $tableKeyFields;
	protected $tableFields;
	protected $dbAdapter;
	protected $sql;
	protected $usePaginator;
	protected $paginatorOptions;
	
	
	/********************* SELECTS BEGIN ************************/	
	public function findAll($table = null, $resultType = 'array', $order = null, $limit = null)
    {
        $select = $this->getSelect($table);
        
        if (!is_null($order)) {
        	$select->order($order);
        }
        $result = $this->selectMany($select, null, $resultType);
        return $result;
    }
    
    public function findAllBy(array $where, $table = null, $resultType = 'array', $order = null, $limit = null) {
    	if (!is_array($where)) {
    		throw new \Exception('O medoto findAllBy espera um array where, ex: array("id" => "1").');
    	}
    	
    	$table = $table ?: $this->table;
    
    	$select = $this->getSelect($table);
    	$select->where($where);
    
    	if ($order) {
    		$select->order($order);
    	}
    	
    	if ($limit) {
    	    $select->limit($limit);
    	}
    	
    	return $this->selectMany($select, null, $resultType);
    }
	
	public function findById($id, $table = null, $resultType = 'array') 
	{
	    $where = array(current($this->getTableKeyFields()) => $id);
		$select = $this->getSelect($table);
		$select->where($where);
		$result = $this->selectOne($select, null, $resultType);
		
		return $result;
	}
	
	public function findOneBy(array $where, $table = null, $resultType = 'array') 
	{
		$select = $this->getSelect($table);
		$select->where($where);
		$result = $this->selectOne($select, null, $resultType);
		
		return $result;
	}
	
	public function selectOne(Select $select, ResultSetInterface $resultSet = null, $resultType = 'array')
	{
		$select->limit(1);
		$result = $this->select($select, $resultSet)->current();
		
		switch ($resultType) {
			case 'object' :
				$result = $this->extract($result, true);
				return $result;
			default:
				return $result;
		}
	}
	
	public function selectMany(Select $select, ResultSetInterface $resultSet = null, $resultType = 'array')
	{
		switch ($resultType) {
			case 'object' :
				$resultSet = new HydratingResultSet(new ClassMethods(), $this->getModel());
				break;
			default:
				break;
		}
		
		if ($this->usePaginator) {
			$this->usePaginator = false;
			$result = $this->initPaginator($select, $resultSet);
		} else {
			$resultMany = $this->select($select, $resultSet);
			foreach ($resultMany as $row) {
				$result[] = $row;
			}
		}
		
		return isset($result) ? $result : false;
	}
	/****************************** SELECTS END ****************************/
	
	
	/**************************** TRANSITION BEGIN ***************************/
	public function insert($data, $table = null, $returnEntity = false)
	{
		$table = $table ?: $this->table;
		$data = $this->extract($data, $returnEntity, $returnEntity);
		
		if ($table === $this->table)
		{
			$data = $this->cleanData($data);
		}
		
		/* if ($table === $this->table && $this->findOneBy($this->dataToTableKeyFields($data))) {
			throw new \RuntimeException('Esse registro já existe no banco de dados');
		} */
		
		$sql = $this->getSql();
		$insert = $sql->insert($table);
		$insert->values($data);
		
		$statement = $sql->prepareStatementForSqlObject($insert);
		$result = $statement->execute();
		
		return $result->getGeneratedValue();
	}
	
	public function update($data, $oldWhere, $table = null, $returnEntity = false)
	{
	    $table = $table ?: $this->table;
		$data = $this->extract($data, $returnEntity);
		
		// verifica se a variável $where não é um array, se não for tratar o como int
		if (!is_array($oldWhere)) {
		    // se não for um array é um numero então faz um cast para int
		    $whereId = (int) $oldWhere;
		    
		    // então monta o where usando o tableKeyFields
		    $where[$this->tableKeyFields[0]] = $whereId;
		} else {
		    // se for um array mantem os dados findos criando a variável where com os mesmos dados vindos do oldWhere
		    $where = $oldWhere;
		}

		if ($table === $this->table) {
			$data = $this->cleanData($data);
		}

		$sql = $this->getSql();
		$update = $sql->update($table);
		$update->set($data)->where($where);
		
		$statement = $sql->prepareStatementForSqlObject($update);
		
		try {
			$result = $statement->execute();
		} catch(\Exception $e) {
		    throw new \RuntimeException($e->getMessage() . "<br /><br />" . $e->getPrevious());
		}

		return true;
	}
	
	public function delete($oldWhere, $table = null)
	{
	    // verifica se a variável $where não é um array, se não for tratar o como int
		if (!is_array($oldWhere)) {
		    // se não for um array é um numero então faz um cast para int
		    $whereId = (int) $oldWhere;
		    
		    // então monta o where usando o tableKeyFields
		    $where[$this->tableKeyFields[0]] = $whereId;
		} else {
		    // se for um array mantem os dados findos criando a variável where com os mesmos dados vindos do oldWhere
		    $where = $oldWhere;
		}
	    
		$table = $table ?: $this->table;
		$sql = $this->getSql();
		$delete = $sql->delete($table)->where($where);
		
		$statemente = $sql->prepareStatementForSqlObject($delete);
		$statemente->execute();
		
		return true;
	}
	/**************************** TRANSITION END ****************************/

	
	/**************************** TREATMENT BEGIN ***************************/
	public function dataToTableKeyFields($data)
	{
		if (!is_array($this->tableKeyFields)) {
			throw new \RuntimeException('Erro no mapper - não foi setados os campos desta tabela: ' . $this->table);
		}
		
		foreach ($this->tableKeyFields as $field) {
			$items[$field] = $data[$field];
		}
		
		return $items;
	}
	
	public function extract($data, $returnEntity = false)
	{
		/**
		 * Se $returnEntity for setado como false o retorno será um array
		 * Se $returnEntity não for setado o valor será true e será retornado uma model
		 */
		if ($returnEntity) {
			$data = $this->getModel($data);
			return $data;
		}
		
		if (is_array($data)) {
			return $data;
		}
		
		/**
		 * $this->model - é o model que contém os getters e setters.
		 */
		if (!$data instanceof $this->model) {
			throw new \InvalidArgumentException('A variável data precisa ser uma instancia do AbstractModel');
		}
		
		$hydrator = new ClassMethods();
		$result = $hydrator->extract($data);
		
		return $result;
	}
	
	public function cleanData($data, $excludeEmpty = true)
	{
		if (!is_array($this->tableFields) || !count($this->tableFields)) {
			return $data;
		}
		
		foreach ($data as $key => $value)
		{
			if (!in_array($key, $this->tableFields)) {
				unset($data[$key]);
			}
			
			if ($excludeEmpty && empty($value))
			{
			    unset($data[$key]);
			}
		}
		
		return $data;
	}
	
	public function getModel(array $data = array())
	{
		if (is_string($this->model) && class_exists($this->model))
		{
			if ($data) {
				$hydrator = new ClassMethods();
				return $hydrator->hydrate($data, new $this->model);
			}
			
			return new $this->model;
		} else {
			throw new \RuntimeException('Não foi possível instanciar o model, verificar se a classe existe e se está configurada corretamente, verificar no arquivo mapper se está setada corretamente');
		}
	}
	
	/********************************** TREATMENT END ***********************************/
	
	
	/********************************** SQL BEGIN ***************************************/
	public function getSql()
	{
		if (!$this->sql instanceof Sql) {
			$this->sql = new Sql($this->getDbAdapter());
		}
		
		return $this->sql;
	}
	
	public function setSql(Sql $sql)
	{
		$this->sql = $sql;
		return $this;
	}
	
	public function getSelect($table = null)
	{
		$table = $table ?: $this->table;
		
		if (empty($table)) {
			throw new \InvalidArgumentException('A tabela para buscar os dados não foi informado pelo getAll e nem pelo mapper, veja no arquivo mapper se a variavel table foi setada');
		}
		
		$select = $this->getSql()->select($table);
		return $select;
	}
	
	public function select(Select $select, ResultSetInterface $resultSet = null)
	{
		$statement = $this->getSql()->prepareStatementForSqlObject($select);
		
		$resultSet = $resultSet ?: new ResultSet(ResultSet::TYPE_ARRAY);
		$resultSet->initialize($statement->execute());
		
		return $resultSet;
	}
	/************************************ SQL END ***********************************/
	
	
	/********************************* PAGINATOR BEGIN ********************************/
	public function getPaginatorOptions()
	{
		return $this->paginatorOptions;
	}
	
	public function setPaginatorOptions($paginatorOptions)
	{
		$this->paginatorOptions = $paginatorOptions;
		return $this;
	}
	
	public function usePaginator(array $paginatorOptions = array())
	{
	    if (!isset($paginatorOptions['cancel'])) {
    		$this->usePaginator = true;
    		$this->paginatorOptions = $paginatorOptions;
	    } elseif ($paginatorOptions['cancel'] = true) {
	        $this->usePaginator = false;
	        //$this->paginatorOptions = array();
	    } else {
	        $this->usePaginator = true;
	    }
        
	    return $this;
	}
	
	public function initPaginator($select, ResultSetInterface $resultSet = null)
	{
		$paginator = new Paginator(new PaginatorDbSelect($select, $this->getDbAdapter(), $resultSet));
		$options = $this->getPaginatorOptions();
		
		if (isset($options['itemCountPerPage'])) {
			$paginator->setItemCountPerPage($options['itemCountPerPage']);
		}
		
		if (isset($options['currentPageNumber'])) {
			$paginator->setCurrentPageNumber($options['currentPageNumber']);
		}
		
		return $paginator;
	}
	/****************************** PAGINATOR END ******************************/

	
	/*********************** GETTERS FOR PROTECTED BEGIN ***********************/
	public function getTable()
	{
		return $this->table;
	}
	
	public function getDbAdapter()
	{
		return $this->dbAdapter;
	}
	
	public function setDbAdapter(Adapter $dbAdapter)
	{
		$this->dbAdapter = $dbAdapter;
		return $this;
	}
	
	/**
	 * @return tableName
	 */
	public function getTableName() {
		return $this->tableName;
	}
	
	/**
	 * @param $tableName
	 * @return self
	 */
	public function setTableName($tableName) {
		$this->tableName = $tableName;
		return $this;
	}
	
	/**
	 * @return tableFields
	 */
	public function getTableFields() {
		return $this->tableFields;
	}
	
	/**
	 * @param $tableFields
	 * @return self
	 */
	public function setTableFields($tableFields) {
		$this->tableFields = $tableFields;
		return $this;
	}
	
	/**
	 * @return tableKeyFields
	 */
	public function getTableKeyFields() {
		return $this->tableKeyFields;
	}
	
	/**
	 * @param $tableKeyFields
	 * @return self
	 */
	public function setTableKeyFields($tableKeyFields) {
		$this->tableKeyFields = $tableKeyFields;
		return $this;
	}
	/************************* GETTERS FOR PROTECTED END ***********************/
	
}