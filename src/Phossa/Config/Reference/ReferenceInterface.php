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

use Phossa\Config\Exception\LogicException;

/**
 * ReferenceInterface
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ReferenceInterface
{
    /**
     * Verify $str has reference in it or not
     *
     * @param  string $str
     * @return false|array ['${name}' => 'name']
     * @access public
     */
    public function hasReference(/*# string */ $str);

    /**
     * Replace all references in the string like '${system.dir}/temp'
     *
     * IF result is array, DO NOT recursively derefence it
     *
     * @param  string $str
     * @return string|object|array
     * @throws LogicException if malformed reference found or unknown ref
     * @access public
     */
    public function deReference(/*# string */ $str);

    /**
     * Replace all references in the array
     *
     * @param  array &$dataArray
     * @return void
     * @throws LogicException if malformed reference found or unknown ref
     * @access public
     */
    public function deReferenceArray(array &$dataArray);

    /**
     * Get referenced value by reference name
     *
     * @param  string $name
     * @return string|array|object
     * @throws LogicException if reference with name unknown
     * @access public
     */
    public function getReferenceValue(/*# string */ $name);
}
