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

namespace Phossa\Config\Cache;

/**
 * CacheInterface
 *
 * Cache all configs into one cache object
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface CacheInterface
{
    /**
     * Configure path etc.
     *
     * @return $this
     * @access public
     */
    public function __invoke();

    /**
     * Save data to a cache
     *
     * @param  array $data
     * @return $this
     * @access public
     */
    public function save(array $data);

    /**
     * Get data from cache
     *
     * @return array|false
     * @access public
     */
    public function get();

    /**
     * Clear all cache
     *
     * @return $this
     * @access public
     */
    public function clear();
}
