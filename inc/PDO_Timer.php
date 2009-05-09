<?php

require_once 'PDOStatement_Timer.php';

class PDO_Timer {
	protected $_queries_log = array();
	protected $_mark_query_time;
	private $_p_sql;
	private $_pdo;

	public function __construct($dsn,$username='',$password='',array $driver_options=array()) {
		$this->_pdo = new PDO($dsn,$username,$password,$driver_options);
		$this->_queries_log = array('PDOStatement' => array());
	}

	private function _autoQuote() {
		$args = func_get_args();
		list($_, $sql) = each($args);
		
		if (count($args) == 1) return $sql;
		
		$params = array();
		while ( list( $_, $val ) = each($args) ) {
			switch(gettype($val)) {
				case 'integer':
					$type = PDO::PARAM_INT;
					break;
				case 'double':
					$type = PDO::PARAM_INT;
					break;
				case 'boolean':
					$type = PDO::PARAM_BOOL;
					break;
				case NULL:
					$type = PDO::PARAM_NULL;
					break;
				case 'string':
				default:
					$type = PDO::PARAM_STR;
			}
			$params[] = $this->_pdo->quote($val, $type);
		}
		return vsprintf($sql, $params);
	}

	public function squery () {
		$args = func_get_args();
		$sql = call_user_func_array(array('self','_autoQuote'), $args);
		$r = $this->query($sql);
		return $r;
	}
	
	/**
	 * Exécute la requête et renvoie tous ses résultats dans un tableau de tableaux, groupés par la colonne $key
	 *
	 * exemple: $result[$row[$key]][] = $row;
	 */
	public function fetchAllGroupBy() {
		$args = func_get_args();
		
		if (count($args) < 2) {
			require_once 'PDO_Timer/PDO_Timer_Exception.php';
			throw new PDO_Timer_Exception('fetchAllGroupBy need more argument ('.count($args).')');
			die;
		}
		
		$key = array_shift($args);
		$query = call_user_func_array(array('self', 'squery'), $args);
		
		$result = array();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$result[$row[$key]][] = $row;
		}
		
		$this->_markQueriesLog();
		return $result;
	}
	
	/**
	 * Exécute la requête et renvoie tous ses résultats dans un tableau indexé sur le champ $key
	 *
     * exemple : $r = $obj->fetchAllAsDict( 'id', "SELECT * FROM users" )
     * $r[1] = user d'ID 1
	 */
	public function fetchAllAsDict() {
		$args = func_get_args();
		
		if (count($args) < 2) {
			require_once 'PDO_Timer/PDO_Timer_Exception.php';
			throw new PDO_Timer_Exception('fetchAllAsDict need more argument ('.count($args).')');
			die;
		}
		
		$key = array_shift($args);
		$query = call_user_func_array(array('self', 'squery'), $args);
		
		$result = array();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$result[$row[$key]] = $row;
		}
		
		$this->_markQueriesLog();
		return $result;
	}
	
	/**
	 * Exécute la requête et renvoie tous ses résultats dans un tableau indexé sur le champ $key
	 *
     * exemple : $r = $obj->fetchAllAsDict( 'id', "SELECT * FROM users" )
     * $r[1] = user d'ID 1
	 */
	public function fetchAllAsDict2() {
		$args = func_get_args();
		
		if (count($args) < 3) {
			require_once 'PDO_Timer/PDO_Timer_Exception.php';
			throw new PDO_Timer_Exception('fetchAllAsDict need more argument ('.count($args).')');
			die;
		}
		
		$key1 = array_shift($args);
		$key2 = array_shift($args);
		$query = call_user_func_array(array('self', 'squery'), $args);
		
		$result = array();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$result[$row[$key1]][$row[$key2]] = $row;
		}
		
		$this->_markQueriesLog();
		return $result;
	}
	
	/**
	 * Exécute la requête et renvoie tous ses résultats dans un tableau indexé clé => valeur.
	 *
	 * La clé est le premier champ du SELECT, la valeur le second.
	 * Pratique pour foreach( $r as $key=>$value ) echo "$key : $value";
	 * exemple: $r = $obj->fetchPairs( 'SELECT id, name FROM table' )
	 * $r[1] = le nom du truc dont l'ID est 1.
	 *
	 */
	public function fetchPairs() {
		$args = func_get_args();
		$query = call_user_func_array(array('self', 'squery'), $args);
		
		$result = array();
		while( $row = $query->fetch(PDO::FETCH_NUM) ) {
			$result[$row[0]] = $row[1];
		}
		$this->_markQueriesLog();
		return $result;
	}
	
	/**
	 * Build & exec insert query
	 */
	public function insert($table, $data) {
		$columns = array();
		$values  = array();
		
		foreach ($data as $key => $value) {
			$columns[] = $key;
			$values[]  = '%s';
		}
		
		$sql = 'INSERT INTO ' . $table . '(' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ')';
		$sql = vsprintf($sql, array_map(array('self', '_autoQuote')), array_values($data));
		return self::squery($sql);
	}
	
	public function update($table,array $data,$where) {
		if (!is_string($where)) {
			require_once 'PDO_Timer/PDO_Timer_Exception.php';
			throw new PDO_Timer_Exception('Argument 3 passed to ' . __CLASS__ . '::' . __FUNCTION__ . ' must be a string, ' . gettype($where) . ' given.');
			die;
		}
		
		$set = array();
		foreach($data as $key => $value) {
			$set[] = $key . '=' . self::_autoQuote($value);
		}
		if ($set) {
			return self::squery('UPDATE ' . $table . ' SET ' . implode(',', $set) . $where );
		} else {
			return false;
		}
	}
	
	public function where($data,array $where=array()) {
		foreach ($data as $key => $value) {
			$where[] = is_null($value) ? $key . ' IS NULL' : $key . '=' . self::_autoQuote($value);
		}
		
		if ($where) {
			return 'WHERE ' . implode(' AND ',$where);
		} else {
			return '';
		}
		
	}
	
	public function sprepare($sql) {
		$this->_p_sql = $sql;
		return $this;
	}
	
	public function sexecute() {
		$args = func_get_args();
		return call_user_func_array(array('self','squery'),array_merge(array($this->_p_sql),$args));
	}
	
	protected function _markQueriesLog($pdostatement=false) {
		if ($pdostatement) {
			$this->_queries_log['PDOStatement'][count($this->_queries_log['PDOStatement'])-1][0] += microtime(true)-$this->_mark_query_time;
		} else {
			$this->_queries_log[count($this->_queries_log)-2][0] += microtime(true)-$this->_mark_query_time;
		}
	}
	
	public function setStatementQueriesLog($logs) {
		//$this->_queries_log['PDOStatement'][] = $logs;
	}
	
	public function getQueriesLog() {
		return $this->_queries_log;
	}
	
	public function __call($name,$args) {
		$t = microtime(true);
		$r = call_user_func_array(array($this->_pdo,$name),$args);

		$this->_mark_query_time = microtime(true);
		$this->_queries_log[] = array($this->_mark_query_time-$t, $name, $args);
		
		if ($r instanceof PDOStatement) {
			return new PDOStatement_Timer($r,$this);
		}
		
		return $r;
	}
}