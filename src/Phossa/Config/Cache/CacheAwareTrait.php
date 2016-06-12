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
 * Implementation of CacheAwareInterface
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     CacheAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait CacheAwareTrait
{
    /**
     * the cache pool object
     *
     * @var    CacheInterface
     * @access protected
     */
    protected $cache;

    /**
     * {@inheritDoc}
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }
}
