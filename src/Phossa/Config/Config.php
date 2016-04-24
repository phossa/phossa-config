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
use Phossa\Config\Loader\LoaderInterface;
use Phossa\Config\Exception\InvalidArgumentException;

/**
 * Config class with support for environment, reference, cache
 *
 * Read in configuration from config files base on environment given.
 *
 * - Environment: environment can be in env file (bash style .env) and can
 *   be loaded into process' environment by Helper\Environment::load(file)
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
class Config extends Parameter
{
    /**
     * the main config directory
     *
     * @var    string
     * @access protected
     */
    protected $directory;

    /**
     * environment like 'production/host1'
     *
     * @var    string
     * @access protected
     */
    protected $environment;

    /**
     * loader
     *
     * @var    LoaderInterface
     * @access protected
     */
    protected $loader;

    /**
     * everything loaded from all config files
     *
     * @var    bool
     * @access protected
     */
    protected $all_loaded = false;

    /**
     * Constructor
     *
     * To disable reference feature: set $referencePattern = ''
     *
     * @param  string $configDirectory the configuration directory
     * @param  string $environment the environment like 'production/host1'
     * @param  string $fileType config file type 'php|ini|xml|json'
     * @param  LoaderInterface $loader
     * @param  string $referencePattern change reference pattern if want to
     * @throws InvalidArgumentException if directory not right
     * @access public
     */
    public function __construct(
        /*# string */ $configDirectory,
        /*# string */ $environment = null,
        /*# string */ $fileType = 'php',
        LoaderInterface $loader = null,
        /*# string */ $referencePattern = null
    ) {
        // set config root directory
        $this->directory = rtrim($configDirectory, '/\\') . DIRECTORY_SEPARATOR;

        // set environment
        $this->environment = trim($environment, '/\\');

        // set loader
        if (null == $loader) {
            $loader = new Loader(); // use default
        }
        $this->loader = $loader($configDirectory, $fileType);

        // init config pool and set reference pattern
        parent::__construct([], $referencePattern);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->loadConfig($key);
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
        return parent::set($key, $value);
    }

    /**
     * Read in config files
     *
     * @param  null|string $key
     * @return this
     * @throws InvalidArgumentException if $key is not a string
     * @access protected
     */
    protected function loadConfig($key)
    {
        // all loaded
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
        // load from files
        $res = $this->loader->load($group, $this->environment);

        // mark pool dirty
        if (count($res)) {
            $this->setPoolDirty();
        }

        // set the whole pool
        if (null === $group) {
            $this->pool = $res ?: [];

        // set a group
        } else {
            $this->pool[$group] = $this->fixValue($res);
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

        // group loaded already
        if (isset($this->pool[$group])) {
            return null;
        }

        // ignore super globals
        if ('_' === $group[0] && isset($GLOBALS[$group])) {
            return null;
        }

        // try load group
        return $this->get($name);
    }
}
