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
use Phossa\Config\Exception\NotFoundException;

/**
 * EnvironmentInterface
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface EnvironmentInterface
{
    /**
     * Load env from a file
     *
     * @param  string $path
     * @return void
     * @throws RuntimeException if parse error
     * @throws NotFoundException if no file found
     * @access public
     * @static
     */
    public static function load(/*# string */ $path);
}
