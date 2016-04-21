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

namespace Phossa\Config;

/**
 *
 *
 * @package Phossa\PACKAGE
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ConfigInterface
{
    /**
     * Get a configure value
     *
     * @param  string $key configuration key
     * @param  null|string|array default value
     * @return null|string|array
     * @access public
     */
    public function get(/*# string */ $key, $default = null);

    /**
     * Set configuration with key
     *
     * @param  string $key configuration key
     * @param  string|array values
     * @return void
     * @access public
     */
    public function set(/*# string */ $key, $value);

    /**
     * Has a configure by key ?
     *
     * @param  string $key configuration key
     * @return bool
     * @access public
     */
    public function has(/*# string */ $key)/*# : bool */;
}
