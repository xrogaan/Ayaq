<?php
/**
* @category Ayaq
* @package Ayaq_PDOStatement_Timer
* @copyright Copyright (c) 2008, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

/**
* @category Ayaq
* @package Ayaq_PDOStatement_Timer
* @copyright Copyright (c) 2008, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/
class PDOStatement_Timer {
	protected $_queries_log;
	protected $_mark_query_time;
	private $_PDOStatement;
	
	public function __construct(PDOStatement $PDOStatement,&$parent) {
		$this->_PDOStatement = $PDOStatement;
		$this->objParent = $parent;
	}
	
	/**
	 * Used to return the logs
	 *
	 * @return array
	 */
	public function getQueriesLog() {
		throw new PDO_Exception('PDOStatement_Timer::getQueriesLog is not implemented yet.');
		//return $this->_queries_log;
	}
	
	public function __call($name, $args) {
		$t = microtime(true);
		$r = call_user_func_array(array($this->_PDOStatement,$name),$args);
		$this->_mark_query_time = microtime(true);
		$this->objParent->setStatementQueriesLog(array($this->_mark_query_time-$t, $name, $args));
		return $r;
	}
}