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
use Phossa\Config\Exception\InvalidArgumentException;

/**
 * LoaderInterface
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface LoaderInterface
{
    /**
     * Set the root directory
     *
     * @param  string $rootDir
     * @param  string config file type
     * @return $this
     * @throws InvalidArgumentException
     * @access public
     */
    public function __invoke(
        /*# string */ $rootDir,
        /*# string */ $fileType = 'php'
    );

    /**
     * Load group config files base on environment. Load all if $group is null.
     *
     * @param  null|string $group
     * @param  string $environment
     * @return null|array
     * @throws LogicException if something goes wrong
     * @access public
     */
    public function load(
        $group,
        /*# string */ $environment = null
    );
}
