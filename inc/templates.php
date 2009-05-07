<?php
/**
* @category News
* @package News_Templates
* @copyright Copyright (c) 2008, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

class templates_exception extends Exception {}

/**
* @author Bellière Ludovic
* @category News
* @package News_Templates
* @copyright Copyright (c) 2008, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/
class templates {
    /**
     * File list
     *
     * @var array
     */
    protected $_files = array();

    /**
     * Path to the templates files
     *
     * @var string
     */
    protected $_templatePath;
    
    protected $_options = array();

    private $_escape = array('htmlentities');
	private $_data = array();

    public function __construct($template_path='./templates/',$options=array()) {
        $this->_templatePath = $template_path;
        $this->_options = array_merge($this->_options,$options);
    }

    /**
     * Return the current tempalte path
     *
     * @return string
     */
    public function getTemplatePath() {
        return $this->_templatePath;
    }

    /**
     * Add a template file
     *
     * @param string $tag Template id
     * @param string $name Template filename.
     * @return void
     */
    public function addFile($tag,$name) {
        $this->_files[$tag] = $name;
		return $this;
    }

    /**
     * Process the templates files by $tag
     * $tag can be an array for multi-templates page.
     *
     * @param array|string $tag
     */
    public function render($tag) {
        try {
			//ob_start();
            if (!is_array($tag)) {
                if (isset($this->_files['_begin'])) {
                    include $this->_file('_begin');
                }
                include $this->_file($tag);
                if (isset($this->_files['_end'])) {
                    include $this->_file('_end');
                }
            } else {
                if (isset($this->_files['_begin'])) {
                    include $this->_file('_begin');
                }
                $tags = $tag;
                unset($tag);
                foreach ($tags as $tag) {
                    include $this->_file($tag);
                }
                if (isset($this->_files['_end'])) {
                    include $this->_file('_end');
                }
           }
//            ob_end_flush();
        } catch (templates_exception $e) {
            die ($e->getMessage());
        } catch (Exception $e) {
            die('ex error: '.$e->getMessage());
        }
    }

    /**
     * Check if the template file is readable and returns its name
     *
     * @param string $tag
     */
    private function _file($tag) {
        if (is_readable($this->_templatePath.$this->_files[$tag])) {
            return $this->_templatePath.$this->_files[$tag];
        } else {
            throw new templates_exception('The file <em>'.$this->_templatePath.$this->_files[$tag]. '</em> isn\'t readable');
        }
    }

    /**
     * Used to set some functions who escape the content
     *
     * @param function $ref
     */
    public function setEscape($ref) {
        if (!in_array($ref,$this->_escape)) {
            $this->_escape[] = $ref;
        }
    }

    /**
     * Used to remove a function from the pool of escape
     *
     * @param function $ref
     * @param boolean $id
     * @return boolean
     */
    public function remEscape($ref,$id=false) {
        if ($id && isset($this->_escape[$id])) {
            unset($this->_escape[$id]);
            return true;
        } elseif (!$id) {
            foreach ($this->_escape as $key => $val) {
                if ($val == $ref) {
                    unset($this->_escape[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Force string $var escaping.
     */
    public function escape($var) {
        foreach($this->_escape as $fnct) {
            if (in_array($fnct, array('htmlentities','htmlspecialchars'))) {
                $var = call_user_func($fnct, $var, ENT_COMPAT);
            } else {
                $var = call_user_func($fnc, $var);
            }
        }
        return $var;
    }
	
	public function set($name, $data) {
		$this->_data[$name] = $data;
	}
    
	public function __set($name,$data) {
		self::set($name,$data);
	}
	
	public function __get($name) {
//		return $this->_data[$name];
		if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Propriété non-définie via __get(): <em>' . $name .
            '</em> dans ' . $trace[0]['file'] .
            ' à la ligne ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
	}
	
    public function __isset($key) {
        $strpos = strpos($key,'_');
        if (!is_bool($strpos) && $strpos !== 0) {
            return isset($this->$key);
        }
        return false;
    }
}