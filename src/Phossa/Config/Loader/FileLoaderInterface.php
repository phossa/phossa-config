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

namespace Phossa\Config\Loader;

use Phossa\Config\Exception\LogicException;

/**
 * FileLoaderInterface
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface FileLoaderInterface
{
    /**
     * Parse file contents into config array
     *
     * @param  string $path file path
     * @return array
     * @throws LogicException if something goes wrong
     * @access public
     * @static
     */
    public static function load(/*# string */ $path)/*# : array */;
}
