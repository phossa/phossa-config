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

use Phossa\Config\Loader\Loader;
use Phossa\Config\Cache\CacheInterface;
use Phossa\Config\Cache\CacheAwareTrait;
use Phossa\Config\Loader\LoaderInterface;
use Phossa\Config\Cache\CacheAwareInterface;
use Phossa\Config\Exception\InvalidArgumentException;
use Phossa\Config\Exception\LogicException;
use Phossa\Config\Message\Message;

/**
 * Config class with support for environment, reference, cache
 *
 * Read in configuration from config files base on environment given.
 *
 * - Environment: environment can be in env file (bash style .env) and can
 *   be loaded into process' environment by Env\Environment::load(file)
 *
 * - Resolving any references in the configs like '${system.dir}'.
 *
 * - Cache the whole configs into one cache file, and load cache on start
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     Parameter
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Config extends Parameter implements CacheAwareInterface
{
    use CacheAwareTrait;

    /**
     * config root directory
     *
     * @var    string
     * @access protected
     */
    protected $directory;

    /**
     * config file loader
     *
     * @var    LoaderInterface
     * @access protected
     */
    protected $loader;

    /**
     * true if all config files loaded
     *
     * @var    bool
     * @access protected
     */
    protected $all_loaded = false;

    /**
     * Constructor
     *
     * - set the root directory holding config files
     *
     * - set current running environment if any
     *
     * - config file type, support php|json|ini|xml
     *
     * - provide a loader object if not using the default one
     *
     * - reference support, default pattern like '${system.dir}'
     *   to disable this feature, set $referencePattern = ''
     *
     * @param  string $configDirectory the configuration directory
     * @param  string $environment the environment like 'production/host1'
     * @param  string $fileType config file type 'php|ini|xml|json'
     * @param  CacheInterface $cache cache pool object if any
     * @param  LoaderInterface $loader
     * @param  string $referencePattern change reference pattern if want to
     *
     * @throws InvalidArgumentException if dir is bad or unsupported file type
     * @access public
     */
    public function __construct(
        /*# string */ $configDirectory,
        /*# string */ $environment = null,
        /*# string */ $fileType = 'php',
        CacheInterface $cache   = null,
        LoaderInterface $loader = null,
        /*# string */ $referencePattern = null
    ) {
        // root directory
        $this->directory = $configDirectory;

        // get from cache
        if (null !== $cache) {
            $this->setCache($cache($configDirectory, $fileType, $environment));
            if (is_array($conf = $cache->get())) {
                parent::__construct($conf, $referencePattern);
                $this->all_loaded = true;
            }

        // init with empty data
        } else {
            parent::__construct([], $referencePattern);
        }

        // set loader
        if (null === $loader) {
            $loader = new Loader(); // use default
        }

        // init loader
        $this->loader = $loader($configDirectory, $fileType, $environment);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        // lazy load
        $this->loadConfig($key);

        // get the $key
        return parent::get($key, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        // null is special
        if (null !== $key) {
            $this->loadConfig($key);
        }

        // set the pair
        return parent::set($key, $value);
    }

    /**
     * User need to make $this->all_loaded is true!
     *
     * {@inheritDoc}
     */
    public function save()
    {
        if (null === $this->cache) {
            throw new LogicException(
                Message::get(Message::CACHE_NOT_READY),
                Message::CACHE_NOT_READY
            );
        }
        $this->loadConfig(null);
        $this->cache->save($this->pool);
        return $this;
    }

    /**
     * Read in config files for $key
     *
     * @param  null|string $key
     * @return $this
     * @throws InvalidArgumentException if $key is not a string
     * @access protected
     */
    protected function loadConfig($key)
    {
        // all config files loaded
        if ($this->all_loaded) {
            return $this;
        }

        // load all
        if (null === $key) {
            $this->loadGroupConfig(null);
            $this->all_loaded = true;
            return $this;
        }

        // $key has to be a string
        $this->exceptionIfNotString($key);

        // first field
        $group = $this->getFirstField($key);

        // group loaded already
        if (isset($this->pool[$group])) {
            return $this;
        }

        // super globals dealed in getValue()
        if ('_' !== $group[0] || !isset($GLOBALS[$group])) {
            $this->loadGroupConfig($group);
        }

        return $this;
    }

    /**
     * Load by group, if $group is null, load all
     *
     * @param  null|string $group
     * @access protected
     */
    protected function loadGroupConfig($group)
    {
        // data from files
        $res = $this->loader->load($group);

        // mark pool dirty
        if (count($res)) {
            $this->setPoolDirty();
        }

        $config = [];
        foreach($res as $grp => $grpData) {
            if (!isset($config[$grp])) {
                $config[$grp] = [];
            }
            foreach ($grpData as $data) {
                $config[$grp] = array_replace_recursive(
                    $config[$grp], $this->fixValue($data)
                );
            }
        }

        // set the whole pool
        if (null === $group) {
            $this->pool = $config;

        // set a group
        } else {
            $this->pool[$group] = $config[$group];
        }
    }

    /**
     * Override ReferenceTrait::resolveUnResolved().
     *
     * This function WILL auto load any unknown reference from config file
     * if the group is not loaded yet
     *
     * {@inheritDoc}
     */
    protected function resolveUnResolved(/*# : string */ $name)
    {
        // get group
        $group = $this->getFirstField($name);

        // group loaded already, $name still unknown, return null
        if (isset($this->pool[$group])) {
            return null;
        }

        // unknown superglobal values, return null
        if ('_' === $group[0] && isset($GLOBALS[$group])) {
            return null;
        }

        // try get $name
        return $this->get($name);
    }
}
