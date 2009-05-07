<?php


class PDOStatement_Timer {
	protected $_queries_log;
	protected $_mark_query_time;
	private $_PDOStatement;
	
	public function __construct(PDOStatement $PDOStatement,&$parent) {
		$this->_PDOStatement = $PDOStatement;
		$this->objParent = $parent;
	}
	
	public function getQueriesLog() {
		return $this->_queries_log;
	}
	
	public function __call($name, $args) {
		$t = microtime(true);
		$r = call_user_func_array(array($this->_PDOStatement,$name),$args);
		$this->_mark_query_time = microtime(true);
		$this->objParent->setStatementQueriesLog(array($this->_mark_query_time-$t, $name, $args));
		return $r;
	}
}