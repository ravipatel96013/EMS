<?php
class Models_SQLCache
{

	private static $instance;
	private $cache = array();
	private $useCache = true;

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) 
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public function getAllCache()
	{
		return $this->cache;
	}

	public function getCache($ckey)
	{
		if ($ckey != '') 
		{
			if ($this->cacheDataAvailable($ckey)) 
			{
				return unserialize($this->cache[$ckey]);
			}
		}
	}

	public function emptyCache()
	{
		unset($this->cache);
		$this->cache = array();
	}

	public function ignoreCache()
	{
		$this->useCache = false;
	}

	public function setCache($ckey, $object)
	{

		$this->cache[$ckey] = serialize($object);
	}

	private function cacheDataAvailable($ckey)
	{
		if (array_key_exists($ckey, $this->cache)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}


	public function generateCacheKey($sql, $bind = array(), $sqlFetchMode)
	{
		$bindStr = '';
		if(!empty($bind))
		{
			foreach($bind as $key=>$val)
			{
				$bindStr .= $key ."=". $val;
			}
		}
		
		return md5($sql . $bindStr . $sqlFetchMode);
	}


	public function getData($sql, $bind, $sqlFetchMode, $cached = true)
	{
		global $db;
		
		$ckey = $this->generateCacheKey($sql, $bind, $sqlFetchMode);
		if (($this->useCache) && ($cached && $this->cacheDataAvailable($ckey))) 
		{
			$object = unserialize($this->cache[$ckey]);
		}
		else 
		{
			$object = $db->fetchAll($sql, $bind, $sqlFetchMode);
			$this->setCache($ckey, $object);
		}
		$this->useCache = true;
		return $object;
	}
	
	public function getOne($sql,$bind,$cached = true)
	{
		global $db;
				
		$ckey = $this->generateCacheKey($sql, $bind, 0);
		if (($this->useCache) && ($cached && $this->cacheDataAvailable($ckey))) 
		{
			$object = unserialize($this->cache[$ckey]);
		}
		else 
		{
			$object = $db->fetchOne($sql, $bind);
			$this->setCache($ckey, $object);
		}
		
		$this->useCache = true;
		
		return $object;
	}
}
?>