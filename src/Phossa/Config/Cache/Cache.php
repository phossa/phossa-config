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

use Phossa\Config\Message\Message;
use Phossa\Config\Exception\InvalidArgumentException;

/**
 * One local filesystem implementation of CacheInterface
 *
 * Store config data into one local cache file
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     CacheInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Cache implements CacheInterface
{
    /**
     * directory to store the cache file
     *
     * @var    string
     * @access protected
     */
    protected $cache_dir;

    /**
     * unique key to identify the cache file
     *
     * @var    string
     * @access protected
     */
    protected $cache_key;

    /**
     * Prefix for cache file
     *
     * @var    string
     * @access protected
     */
    protected $prefix = 'cache.conf.';

    /**
     * Set cache directory
     *
     * @throws InvalidArgumentException
     * @access public
     */
    public function __construct(/*# string */ $dir)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(
                Message::get(Message::CACHE_DIR_INVALID, $dir),
                Message::CACHE_DIR_INVALID
            );
        }

        if (!is_writable($dir)) {
            throw new InvalidArgumentException(
                Message::get(Message::CACHE_DIR_NONWRITABLE, $dir),
                Message::CACHE_DIR_NONWRITABLE
            );
        }

        $this->cache_dir = rtrim($dir, '/\\') . \DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        $this->cache_key = md5(serialize(func_get_args()));
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function save(array $data)
    {
        file_put_contents($this->getFileName(), serialize($data));
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        $file = $this->getFileName();
        if (is_file($file)) {
            $str = file_get_contents($file);
            if ($str) {
                return unserialize($str);
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $files = glob($this->cache_dir . $this->prefix . '*');
        foreach ($files as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
        return $this;
    }

    /**
     * The cache file
     *
     * @return string
     * @access protected
     */
    protected function getFileName()/*# : string */
    {
        return $this->cache_dir . $this->prefix . $this->cache_key;
    }
}
