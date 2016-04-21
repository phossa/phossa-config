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

namespace Phossa\Config\Helper;

use Phossa\Config\Exception\RuntimeException;

/**
 * CompilerInterface
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface CompilerInterface
{
    /**
     * Compile (dereferencing) the config array
     *
     * @param  array $config
     * @return array
     * @throws RuntimeException
     * @access public
     */
    public function compile(array $config)/*# : array */;
}