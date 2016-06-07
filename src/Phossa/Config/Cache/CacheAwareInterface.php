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

use Phossa\Config\Exception\LogicException;
interface CacheAwareInterface
{
    /**
     * Set the cache
     *
     * @param  CacheInterface $cache
     * @return self
     * @access public
     */
    public function setCache(CacheInterface $cache);

    /**
     * Write current data to cache
     *
     * @return self
     * @throws LogicException cache object not set
     * @access public
     */
    public function save();
}
