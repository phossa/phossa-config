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

use Phossa\Config\Exception\InvalidArgumentException;

/**
 * ConfigInterface
 *
 * @package Phossa\PACKAGE
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ConfigInterface
{
    /**
     * Get a configure value, if $key is null, get all configs
     *
     * @param  null|string $key configuration key
     * @param  null|string|array default value
     * @return null|string|array
     * @throws InvalidArgumentException if $key not a string
     * @access public
     */
    public function get($key, $default = null);

    /**
     * Set configuration, if $key is null, set ALL configs
     *
     * @param  null|string $key configuration key
     * @param  string|array values
     * @return self
     * @throws InvalidArgumentException if $key not a string
     * @access public
     */
    public function set($key, $value);

    /**
     * Has a configure by key ?
     *
     * @param  string $key configuration key
     * @return bool
     * @throws InvalidArgumentException if $key not a string
     * @access public
     */
    public function has(/*# string */ $key)/*# : bool */;
}
