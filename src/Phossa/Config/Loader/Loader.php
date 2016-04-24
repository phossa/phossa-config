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
use Phossa\Config\Exception\LogicException;

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
        /*# string */ $fileType = 'php'
    ) {
        if (!is_string($rootDir) ||
            !is_dir($rootDir) ||
            !is_readable($rootDir)
        ) {
            throw new InvalidArgumentException(
                Message::get(Message::CONFIG_DIR_INVALID, $rootDir),
                Message::CONFIG_DIR_INVALID
            );
        } else {
            $this->root_dir  = $rootDir;
            $this->file_type = $fileType;
            $class = __NAMESPACE__ . '\\' . ucfirst($fileType) . 'Loader';

            // not supported config type
            if (!class_exists($class, true)) {
                throw new InvalidArgumentException(
                    Message::get(Message::CONFIG_SUFFIX_UNKNOWN, $fileType),
                    Message::CONFIG_SUFFIX_UNKNOWN
                );
            }
            $this->loader_class = $class;

            return $this;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function load(
        $group,
        /*# string */ $environment = null
    ) {
        $subdirs = [''];
        if (null !== $environment) {
            $subdirs = array_merge(
                $subdirs,
                preg_split('/[\/\\\]/', $environment, 0, \PREG_SPLIT_NO_EMPTY)
            );
        }

        $path  = $this->root_dir;
        $class = $this->loader_class;
        $conf  = [];
        foreach($subdirs as $dir)
        {
            $path .= $dir . DIRECTORY_SEPARATOR;
            if (null === $group) {
                $files = glob("{$path}*.{$this->file_type}");
            } else {
                $files = [ "{$path}{$group}.{$this->file_type}" ];
            }

            foreach ($files as $file) {
                if (is_file($file)) {
                    $conf = array_replace_recursive($conf, $class::load($file));
                } else {
                    throw new LogicException(
                        Message::get(Message::CONFIG_LOAD_ERROR, $file),
                        Message::CONFIG_LOAD_ERROR
                    );
                }
            }
        }
        return $conf ?: null;
    }
}
