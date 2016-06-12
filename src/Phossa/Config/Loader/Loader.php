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

use Phossa\Config\Message\Message;
use Phossa\Config\Exception\InvalidArgumentException;

/**
 * Config file loader
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     LoaderInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Loader implements LoaderInterface
{
    /**
     * Root directory
     *
     * @var    string
     * @access protected
     */
    protected $root_dir;

    /**
     * subdirectories to load files
     *
     * @var    array
     * @access protected
     */
    protected $sub_dirs;

    /**
     * config file suffix/type
     *
     * @var    string
     * @access protected
     */
    protected $file_type;

    /**
     * Fileloder class
     *
     * @var    string
     * @access protected
     */
    protected $loader_class;

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        /*# string */ $rootDir,
        /*# string */ $fileType = 'php',
        /*# string */ $environment = null
    ) {
        return $this->setRootDir($rootDir)
                    ->setFileType($fileType)
                    ->setEnvironment($environment);
    }

    /**
     * {@inheritDoc}
     */
    public function load($group, $environment = null)
    {
        // reset environment
        if (null !== $environment) {
            $this->setEnvironment($environment);
        }

        $path  = $this->root_dir;
        $sepr  = \DIRECTORY_SEPARATOR;
        $class = $this->loader_class;
        $data  = [];
        foreach($this->sub_dirs as $dir) {
            // construct new path with each sub dir
            $path = rtrim($path . $sepr . $dir, '/\\');

            // load all config files
            if (null === $group) {
                $files = glob("{$path}{$sepr}*.{$this->file_type}");

            // load one type of group files
            } else {
                $files = [ "{$path}{$sepr}{$group}.{$this->file_type}" ];
            }

            foreach ($files as $file) {
                if (is_file($file)) {
                    $grp = basename($file, '.' . $this->file_type);
                    $data[$grp][] = $class::load($file);
                }
            }
        }
        return $data;
    }

    /**
     * Set root directory
     *
     * @param  string $rootDir
     * @return self
     * @throws InvalidArgumentException if dir is bad
     * @access protected
     */
    protected function setRootDir(/*# string */ $rootDir)
    {
        // validate root directory
        if (!is_string($rootDir) || !is_dir($rootDir) || !is_readable($rootDir))
        {
            throw new InvalidArgumentException(
                Message::get(Message::CONFIG_DIR_INVALID, $rootDir),
                Message::CONFIG_DIR_INVALID
            );
        }
        $this->root_dir = rtrim($rootDir, '/\\');

        return $this;
    }

    /**
     * Set config file type
     *
     * @param  string $fileType
     * @return self
     * @throws InvalidArgumentException if unsupported file type
     * @access protected
     */
    protected function setFileType(/*# string */ $fileType)
    {
        // validate file type
        $class = __NAMESPACE__ . '\\' . ucfirst($fileType) . 'Loader';
        if (!class_exists($class, true)) {
            throw new InvalidArgumentException(
                Message::get(Message::CONFIG_SUFFIX_UNKNOWN, $fileType),
                Message::CONFIG_SUFFIX_UNKNOWN
            );
        }
        $this->file_type = $fileType;
        $this->loader_class = $class;

        return $this;
    }

    /**
     * Set environment
     *
     * @param  null|string $environment
     * @return self
     * @access protected
     */
    protected function setEnvironment($environment)
    {
        $subdirs = [''];
        if (null !== $environment) {
            $subdirs = array_merge($subdirs, preg_split('/[\/\\\]/',
                trim($environment, '/\\'), 0, \PREG_SPLIT_NO_EMPTY));
        }
        $this->sub_dirs = $subdirs;

        return $this;
    }
}
