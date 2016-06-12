<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa\Config
 * @copyright 2015 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa\Config\Reference;

/**
 * Tree structure related methods
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait TreeTrait
{
    /**
     * tree node splitter like the '.' in 'db.auth.user'
     *
     * @var    string
     * @access protected
     */
    protected $field_splitter = '.';

    /**
     * Get the first part in the key
     *
     * @param  string $key
     * @return string
     * @access protected
     */
    protected function getFirstField(/*# string */ $key)
    {
        $parts = explode($this->field_splitter, $key);
        return array_shift($parts);
    }

    /**
     * Return a node in an array structure
     *
     * @param  string $key something like 'db.auth.host'
     * @param  array &$tree the array tree
     * @param  bool $create create the node if missing
     * @return null|array|string the matching node
     * @access protected
     */
    protected function &searchTree(
        /*# string */ $key,
        /*# array */ &$tree,
        /*# bool */ $create = false
    ) {
        $found = &$tree;
        $parts = explode($this->field_splitter, $key);
        while (null !== ($part = array_shift($parts))) {
            if (!isset($found[$part])) {
                if ($create) {
                    $found[$part] = [];
                } else {
                    $bad = null;
                    return $bad;
                }
            }
            $found = &$found[$part];
        }
        return $found;
    }

    /**
     * convert [ 'database.dsn' => 'xxx' ] to ['database' => [ 'dsn' => 'xxx' ]]
     *
     * @param  array $value
     * @return array
     * @access protected
     */
    protected function fixValue(array $value)/*# : array */
    {
        $result = [];
        foreach ($value as $k => $v) {
            if (false !== strpos($k, $this->field_splitter)) {
                $res = &$this->searchTree($k, $result, true);
                $res = is_array($v) ? $this->fixValue($v) : $v;
            } else {
                $result[$k] = is_array($v) ? $this->fixValue($v) : $v;
            }
        }
        return $result;
    }
}
