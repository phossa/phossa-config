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

namespace Phossa\Config\Env;

use Phossa\Config\Message\Message;
use Phossa\Config\Exception\LogicException;
use Phossa\Config\Exception\NotFoundException;

/**
 * One implementation of EnvironmentInterface
 *
 * - Load ENV from local file system, normally a '.env' file.
 * - support ${__DIR__} and ${__FILE__} in the '.env' file
 * - Other implmentationmay just need to override `getContents()` method
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     EnvironmentInterface
 * @version 1.0.3
 * @since   1.0.0 added
 * @since   1.0.3 added support for ${__DIR__} and ${__FILE__}
 */
class Environment implements EnvironmentInterface
{
    /**
     * ENV variable name pattern
     *
     * started with ascii and '_', followed by alphanumeric, '.' and '_'
     *
     * @var    string
     * @access protected
     */
    protected $name_pattern  = '[a-zA-Z_][a-zA-Z0-9_.]*+';

    /**
     * ENV variable value pattern
     *
     * Started with non-(space|#) upto # or EOL, spaces are allowed in between
     *
     * @var    string
     * @access protected
     */
    protected $value_pattern = '[^\s#\r\n][^#\r\n]*';

    /**
     * {@inheritDoc}
     */
    public function load(/*# string */ $path)
    {
        $contents = $this->getContents($path);

        // parse & set env
        foreach ($this->parse($contents) as $name => $val) {
            // dereference if need to
            $val = $this->deReference($val);

            // set ENV
            putenv("${name}=${val}");
        }
    }

    /**
     * Read contents from a local file system
     *
     * @param  string $path
     * @return string
     * @access protected
     * @throws NotFoundException if no file found
     */
    protected function getContents(/*# string */ $path)/*# string */
    {
        // read in file
        $contents = @file_get_contents($path);

        // failed
        if (false === $contents) {
            throw new NotFoundException(
                Message::get(Message::CONFIG_FILE_NOTFOUND, $path),
                Message::CONFIG_FILE_NOTFOUND
            );
        }

        // set some magic environment values
        $this->setMagicEnv($path);

        return $contents;
    }

    /**
     * parse and return name/value pairs in array
     *
     * @param  string $contents
     * @return array
     * @access protected
     */
    protected function parse(/*# string */ $contents)/*# : array */
    {
        $result = [];
        $regex = sprintf(
            '/^\s*+(%s)\s*+=\s*+(%s)(?:#.*+)?\s*$/m',
            $this->name_pattern,
            $this->value_pattern
        );
        if (preg_match_all($regex, $contents, $matched, \PREG_SET_ORDER)) {
            foreach ($matched as $m) {
                $result[$m[1]] = trim($m[2]);
            }
        }
        return $result;
    }

    /**
     * dereferencing string like '${USER}' into 'realUser'
     *
     * @param  string $value
     * @return string
     * @throws LogicException if error happens
     * @access protected
     */
    protected function deReference(/*# string */ $value)/*# : string */
    {
        $regex = sprintf('/\${(%s)}/', $this->name_pattern);
        while (false !== strpos($value, '${')) {
            $value = preg_replace_callback(
                $regex,
                function ($matched) {
                    $env = $this->matchEnv($matched[1]);
                    if (false === $env) {
                        throw new LogicException(
                            Message::get(
                                Message::CONFIG_ENV_UNKNOWN, $matched[1]
                            ),
                            Message::CONFIG_ENV_UNKNOWN
                        );
                    } else {
                        return $env;
                    }
                },
                $value
           );
        }
        return $value;
    }

    /**
     * Find the env value base one the name
     *
     * - support super globals like '_SERVER.HTTP_HOST' etc.
     * - use getenv()
     *
     * @param  string $name
     * @return string|false
     * @access protected
     */
    protected function matchEnv(/*# string */ $name)
    {
        if ('_' === $name[0]) {
            // PHP super globals like _SERVER, _COOKIE etc.
            $pos = strpos($name, '.');
            if (false !== $pos) {
                $pref = substr($name, 0, $pos);
                $suff = substr($name, $pos + 1);
                if (isset($GLOBALS[$pref][$suff])) {
                    return $GLOBALS[$pref][$suff];
                }
            }
            return false;
        } else {
            // normal environment
            return getenv($name);
        }
    }

    /**
     * Set magic env like __DIR__, __FILE__ for $path
     *
     * @param  string $path
     * @access protected
     * @since  1.0.3
     */
    protected function setMagicEnv(/*# string */ $path)
    {
        $real = realpath($path);
        putenv('__DIR__=' . dirname($real));
        putenv('__FILE__=' . basename($real));
    }
}
