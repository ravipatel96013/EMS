<?php
class Models_SiteConfig extends TinyPHP_ActiveRecord 
{
	public $tableName = "site_config";
	
	private $configs = array();
	public $configKey = '';
	public $configValue = '';
	public $configType = '';
	
	
	public $dbIgnoreFields = array('id', 'configs');

	
	public function init()
	{
		$result = $this->getAll(array('configKey', 'configValue'), null, null, null, null, null, Zend_Db::FETCH_ASSOC);
		foreach ($result as $row)
		{
			$this->configs[$row['configKey']] = $row['configValue'];
		}
	}

	
	public function get($key)
	{
	    if (array_key_exists($key, $this->configs))
		{
		    return $this->configs[$key];
		}
	}

	
	public function updateValue($key, $val)
	{
	    if (array_key_exists($key, $this->configs))
		{
			$fields = array('configValue');

			$this->configValue = $val;

			$whereClause = "configKey='$key'";

			$this->execute($this->tableName, $fields, self::DB_EXECMODE_UPDATE, $whereClause);
		}
	}
}
?>