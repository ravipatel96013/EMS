<?php

abstract class TinyPHP_ActiveRecord {

	private static $db;
	private static $hasActiveTransaction = false;
	private $transactionActivatedByCurrentClass = false;
	public $isEmpty;
	public $tableName;
	public $id = 0;
	public $idField;
	private $error_list = array();
	private $tableInfo;
	private $dbEventListeners = array();
	private $updatedRows = 0;
	private $deletedRows = 0;
	private $__currentAction = "";
	private $_models = array();
	private $parentModelHandles = array();
	private $applicationIgnoreFields = array('db', 'isEmpty', 'tableName', 'error_list', 'idField', 'ignoreFieldList', 'applicationIgnoreFields', 'dbIgnoreFields', 'tableInfo', 'dbEventListeners', 'updatedRows', 'deletedRows', '__currentAction', '_models' , 'parentModelHandles','transactionActivatedByCurrentClass', 'lazyLoadProperties');
	private $ignoreFieldList = array();
	private $lazyLoadProperties = array();
	
	const DB_EXECMODE_INSERT  = 1;
	const DB_EXECMODE_UPDATE  = 2;
	const DB_FETCHMODE_ASSOC = 2;
	const DB_FETCHMODE_OBJECT = 5;

	final public function __construct($id = 0,$use_cache=true) {

		global $db;

		self::$db = & $db;


		if ($this->tableName != "") {

			$this->tableInfo = self::$db->describeTable($this->tableName);

		}

		$this->isEmpty = true;

		if ($id != 0) {
			$this->fetchById($id,'*',$use_cache);
		}

		$this->init();
	}

	abstract public function init();
	
	
	private function getter($property,&$value)
	{
	    
	    if(method_exists($this, 'lazyLoadProperty'))
	    {
	        if(in_array($property, $this->lazyLoadProperties))
	        {
	            return $value = $this->lazyLoadProperty($property);
	        }
	        
	    }
	    
	}
	
	final public function hasActiveTransaction()
	{
		return self::$hasActiveTransaction;
	}

	
	final public function addLazyLoadProperty($prop)
	{
	    array_push($this->lazyLoadProperties, $prop);
	}
	
	final public function start_transaction() {

		if(!self::$hasActiveTransaction)
		{
			self::$db->beginTransaction();
			self::$hasActiveTransaction = true;
			$this->transactionActivatedByCurrentClass = true;
			return true;
		}		
		else
		{
			$this->transactionActivatedByCurrentClass = false;
			return false;
		}

	}

	final public function commit() {

		if(self::$hasActiveTransaction && $this->transactionActivatedByCurrentClass)
		{
			self::$db->commit();
			self::$hasActiveTransaction = false;
			$this->transactionActivatedByCurrentClass = false;
		}
		

	}

	final public function rollback() {
		
		if(self::$hasActiveTransaction && $this->transactionActivatedByCurrentClass)
		{
			if($this->_getCurrentAction() == "create") $this->id=0;
			self::$db->rollBack();
			self::$hasActiveTransaction = false;
			$this->transactionActivatedByCurrentClass = false;
		}
		

	}


	public function __get($property) 
	{
	    $value = null;
	    
	    if(is_null($this->getter($property, $value)))
	    {
	        trigger_error(get_class($this) ." GET Error: Undefined property $property", E_USER_NOTICE);
	    }
	    else
	    {
	        return $value;
	    }	
	    
		
	}

	public function __set($property, $value) 
	{
		trigger_error(get_class($this) . " SET Error: Undefined property $property", E_USER_NOTICE);
	}
	
	
	public function getDB() {
		return self::$db;
	}

	public function _getCurrentAction() {
		return $this->__currentAction;
	}

	private function getDbFieldType($fieldName) {

		return @$this->tableInfo[$fieldName]['DATA_TYPE'];

		/**
		 foreach ($this->tableInfo as $info) {
			if ($info['COLUMN_NAME'] == $fieldName) {
			return $info['DATA_TYPE'];
			break;
			}
			}*/
	}

	public function getPostData($ignore_list = array()) {
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$post_fields = $_POST;
			if (is_array($post_fields)) {
				foreach ($post_fields as $key => $val) {
					if (is_array($ignore_list) && !in_array($key, $ignore_list)) {
						if (property_exists($this, $key)) {
							$this->{$key} = $val;
						}
					}
				}
			}
		}
	}

	public function objectsToArray($objects, $fields) {
		$data = array();
		foreach ($objects as $object) {
			$tmp = array();
			foreach ($fields as $field) {
				array_push($tmp, $object->{$field});
			}
			$data[] = $tmp;
			reset($fields);
		}
		return $data;
	}

	public function fieldList($object, $ignore_list = array(), $field_prefix = "") {
		//@@TODO: ability to ignore desired properties from object.

		$fields = array_keys(get_object_vars($object));

		//do ignore list
		if (is_array($ignore_list)) {
			foreach ($ignore_list as $val) {
				if (in_array($val, $fields)) {
					array_splice($fields, array_search($val, $fields), 1);
				}
			}
		}

		//do prefixing
		if ($field_prefix != "") {
			foreach ($fields as $key => $field) {
				$fields[$key] = $field_prefix . $field;
			}
		}


		return implode(",", $fields);
	}

	public function execute($tableName, $fields = array(), $mode = self::DB_EXECMODE_INSERT , $where = "") {

		$field_values = array();

		$this->ignoreFieldList = array_merge($this->applicationIgnoreFields, $this->dbIgnoreFields);



		if (count($fields) == 0) 
		{
			$objectVars = get_object_vars($this);
			foreach ($objectVars as $key => $val) 
			{
				if (!in_array($key, $this->ignoreFieldList)) 
				{
					if (is_null($this->{$key})) 
					{
					    $field_values[$key] = $this->{$key};
					}
					else
					{
					    $field_values[$key] = stripslashes($this->{$key});
					}
				}
			}
		} else {
			if(is_array($fields))
			{
				foreach ($fields as $val) 
				{
				    if (is_null($this->{$val}))
				    {
				        $field_values[$val] = $this->{$val};
				    }
				    else
				    {
				        $field_values[$val] = stripslashes($this->{$val});
				    }
				}
			}
		}


		if($mode ==  self::DB_EXECMODE_INSERT)
		{
			$res = self::$db->insert($tableName,$field_values);
			return self::$db->lastInsertId($tableName);
		}
		else
		{
			$res = self::$db->update($tableName, $field_values,$where);
			return $res;
		}


		//$res = self::$db->autoExecute($tableName, $field_values, $mode, $where);
		/*	if ($mode == DB_AUTOQUERY_INSERT) {
			return self::$db->getOne("SELECT LAST_INSERT_ID()");
		} else {
		return self::$db->affectedRows();
		}*/
	}

	public static function insertMultiple($tableName, $fields, $data) {
		
		foreach($data as $row)
		{
			foreach($fields as $key=>$field)
			{
				$field_values[$field] =  $row[$key];
			}
			$res = self::$db->insert($tableName,$field_values);	
		}		
		return true;
	}

	public function fetchById($id, $field_list = "*", $use_cache=true) {
		$this->__currentAction = "init";
		$sql = "SELECT $field_list FROM " . $this->tableName . " WHERE " . (empty($this->idField) ? "id" : $this->idField) . " = $id LIMIT 0,1";
		$res = $this->findAll($sql, array(), self::DB_FETCHMODE_ASSOC, $use_cache);
		
		if (is_array($res) && count($res) > 0) {
		    $row = $res[0];
			$this->fillObjectVars($row);
			$this->isEmpty = false;
		}
	}

	public function fetchByProperty($property, $property_value, $field_list = "*", $use_cache=true) {

		$this->__currentAction = "init";

		$where_clause = "";

		if(is_string($property))
		{
			$where_clause = " $property = '$property_value'";
		}
		else if(is_array($property) && is_array($property_value))
		{
			$tmp = array();
			foreach($property as $key=>$prop)
			{
				array_push($tmp, "$prop = '". $property_value [$key] ."'");
			}
			if(count($tmp)>0)
			{
				$where_clause = implode(' AND ', $tmp);
			}
		}




		$sql = "SELECT $field_list FROM " . $this->tableName . " WHERE $where_clause LIMIT 0,1";
		//echo $sql;
		$res = $this->findAll($sql, array(), self::DB_FETCHMODE_ASSOC,$use_cache);
		if (is_array($res) && count($res) > 0) {
		    $row = $res[0];
			$this->fillObjectVars($row);
			$this->isEmpty = false;
		}

		$this->init();
	}

	private function fillObjectVars($row) {
		foreach ($row as $key => $val) {
			if ($val !== NULL) {
				$fieldType = $this->getDbFieldType($key);
				if (property_exists($this, $key)) {
					if (in_array($fieldType, array("date", "time", "datetime"))) {
						if ($val != "") {
							$this->{$key} = $val;
						}
					} else {
						$this->{$key} = $val;
					}
				}
			}
		}
	}

	public function getAll($_fields = array(), $filter = "", $order_by = array(), $offset = null, $limit = null, $distinct = false, $fetch_mode =  Zend_DB::FETCH_OBJ , $use_cache=true) {


		if (empty($_fields)) {
			$field_list = "*";
		} else {
			$field_list = implode(",", $_fields);
		}
		$sql = "SELECT ";
		if ($distinct) {
			$sql .= " DISTINCT ";
		}
		$sql .= " $field_list FROM {$this->tableName} ";

		if (!empty($filter)) {
			$sql .= " WHERE " . $filter;
		}

		if (!empty($order_by)) {
			$orderByClause = " ORDER BY ";
			foreach ($order_by as $key => $val) {

				$orderByClause .= " $key  $val,";
			}
			$orderByClause = rtrim($orderByClause, ",");
			$sql .= $orderByClause;
		}

		if (!empty($offset)) {
			$sql .= " LIMIT " . $offset;

			if (!empty($limit)) {
				$sql .= " $limit ";
			}
		}
		global $dataCache;
			
		return $dataCache->getData($sql,array(),$fetch_mode, $use_cache);
		//return self::$db->getAll($sql, null, $fetch_mode);
	}

	public static function findAll($query, $bind = array(), $fetch_mode = self::DB_FETCHMODE_OBJECT, $use_cache=true) {
		global $dataCache;
		
		return $dataCache->getData($query, $bind ,$fetch_mode, $use_cache);

	}

	public static function query($sql, $bind=array()) {
		$result = self::$db->query($sql,$bind);

		return $result;
	}

	public static function getOne($sql,$bind=array()) {

		return self::$db->fetchOne($sql,$bind);
	}

	public static function getCol($sql,$bind=array()) {

		return self::$db->fetchCol($sql,$bind);
	}



	final public function getDeletedRows() {
		return $this->deletedRows;
	}

	final public function getUpdatedRows() {
		return $this->updatedRows;
	}

	public function addListener($event, $call_back, $params = array()) {
		$this->dbEventListeners[$event] = array('call_back' => $call_back, 'params' => $params);
	}

	public function update($fields = array()) {
		$this->__currentAction = "update";

		$result = $this->_notify('beforeUpdate');

		if ($result) {
			$where_clause = "id=" . $this->id;

			try {

				$this->updatedRows = $this->execute($this->tableName, $fields, self::DB_EXECMODE_UPDATE, $where_clause);					

			}
			catch (Zend_Exception $e) {
				
				$this->addError("Exception occured when updating object of  ". $this->tableName ." " . $e->getMessage());
			}
				
				
			if(!$this->hasErrors())
			{
				$this->_notify('afterUpdate');
			}
				
			if($this->hasErrors())
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}

	public function create() {

		$this->__currentAction = "create";

		$result = $this->_notify('beforeCreate');

		
		if ($result) {
			
			$fields = array();

			try {
				

				$this->id = $this->execute($this->tableName, $fields, self::DB_EXECMODE_INSERT);

				
				if($this->id>0)
				{
					
					$this->_notify('afterCreate');
				}
					
			}
			catch (PDOException $e) {
					
				$this->addError("Exception occured when creating object of  ". $this->tableName ." " . $e->getMessage());
			}
			catch (Zend_Exception $e) {
				
				$this->addError("Exception occured when creating object of  ". $this->tableName ." " . $e->getMessage());
			}
			

		}

		if($this->hasErrors())
		{
			return 0;
		}
		else
		{
			return $this->id;
		}
	}

	public function delete($whereClause = "") {

		$this->__currentAction = "delete";

		$deleteWhere = "";
		if ($whereClause != "") {
			$deleteWhere = $whereClause; //donot include KEYWORD WHERE
		} else {
			$deleteWhere = " id = " . $this->id;
		}

		$result = $this->_notify('beforeDelete');

		if ($result) {

			$deleteStmt = "DELETE FROM " . $this->tableName . " WHERE " . $deleteWhere;


			$result = $this->query($deleteStmt);

			if($result->rowCount()>0){

				$this->deletedRows = $result->rowCount();

				$this->_notify('afterDelete');
			}
		}
	}

	private function _notify($event) {
		
		if (array_key_exists($event, $this->dbEventListeners)) {
			$eventSubscriber = $this->dbEventListeners[$event];
			return call_user_func_array($eventSubscriber['call_back'], $eventSubscriber['params']);
		} else {
			return true;
		}
	}

	public function addError($errorMsg, $index = null) {
		if (empty($index)) {
			array_push($this->error_list, $errorMsg);
		} else {
			if (isset($this->error_list[$index])) {
				trigger_error("Error message already exists at the specified index $index", E_USER_WARNING);
			}
			$this->error_list[$index] = $errorMsg;
		}
	}

	public function addErrors($errors) {

		if(is_array($errors))
		{
			foreach($errors as $err)
			{
				$this->addError($err);
			}
		}

	}

	public function getErrors($index = null) {

		if (empty($index)) {
			return $this->error_list;
		} else {
			if (!isset($this->error_list[$index])) {
				trigger_error("No error message exists at the specified $index", E_USER_WARNING);
			} else {
				return $this->error_list[$index];
			}
		}
	}

	public function hasErrors() {

		if (count($this->getErrors()) == 0) {
			return false;
		} else {
			return true;
		}
	}

	public function isError($object) {

		if (PEAR::isError($object)) {
			return true;
		} else {
			return false;
		}
	}

	public function addParentModel($modelHandle, $modelClass, $fkName) {

		if (!array_key_exists($modelHandle, $this->parentModelHandles)) {
			$this->parentModelHandles[$modelHandle] = array('model' => $modelClass, 'fk' => $fkName);
		} else {
			if($this->parentModelHandles[$modelHandle]['model'] !=$modelClass)
			{
				trigger_error('Invalid model handle ' . $modelHandle . ' specified which already registered to a model', E_USER_NOTICE);
			}
		}
	}

	//lazy load function
	public function loadParentModel($_modelHandle, $_idValue = 0, $_property = 'id', $_cached = true) {

		if (!array_key_exists($_modelHandle, $this->parentModelHandles)) {
			trigger_error('Invalid model handle ' . $_modelHandle . ' specified which is not registered to a model', E_USER_ERROR);
			return;
		} else {
			
			$modelClassName = $this->parentModelHandles[$_modelHandle]['model'];
			if (empty($_idValue)) {
				$idValue = $this->{$this->parentModelHandles[$_modelHandle]['fk']};    //id value is retrieved from current objects foriegn key attribute value
			} else {
				$idValue = $_idValue;
			}

			$modelKey = md5($modelClassName . "_" . $idValue . "_" . $_property);

			if ($_cached && array_key_exists($modelKey, $this->_models)) {
				//echo "load from cache";
				return $this->_models[$modelKey];
			} else {
				// echo "create new";
				$obj = new $modelClassName();
				$obj->fetchByProperty($_property, $idValue,'*', false);
				$this->_models[$modelKey] = $obj;
				return $obj;
			}
		}
	}
	
	
	public function loadModel($modelClassName,$property,$propertyValue,$useCache=true)
	{
		global $dataCache;
		
		
		
		if(class_exists($modelClassName,true))
		{
			if($useCache == false)
			{
				$dataCache->ignoreCache();
			}
			
			if($property == 'id' && !is_array($propertyValue))
			{
				$obj = new $modelClassName($propertyValue);				
			}
			else
			{
				$obj = new $modelClassName();
				$obj->fetchByProperty($property,$propertyValue);			
			}	
					
			return $obj;
		}
		else
		{
			trigger_error("$modelClassName does not exist. Please make sure you typed the correct class namd and required file exists", E_USER_ERROR);
		}
			
	}

	public function refreshById($id)
	{
		global $dataCache;
		$dataCache->ignoreCache();
		$this->fetchById($id);
		$this->init();

	}

	public function refreshByProperty($property, $property_value, $field_list = "*")
	{
		global $dataCache;
		$dataCache->ignoreCache();
		$this->fetchByProperty($property, $property_value, $field_list = "*");
	}
	
	public function attachEventHandler($_eventName, $_handlerClass)
	{
		$appEvt = TinyPHP_AppEvent::getInstance();
		$appEvt->attachHandler($_eventName,$_handlerClass);
	}

}

?>