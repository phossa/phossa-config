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

namespace Phossa\Config\Helper;

use Phossa\Config\Message\Message;
use Phossa\Shared\Pattern\StaticAbstract;
use Phossa\Config\Exception\LogicException;
use Phossa\Config\Exception\NotFoundException;

/**
 * Load env from a file
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     EnvironmentInterface
 * @see     StaticAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Environment extends StaticAbstract implements EnvironmentInterface
{
    /**
     * env variable name pattern
     *
     * @var    string
     */
    const NAME_PATTERN  = '[a-zA-Z_][a-zA-Z0-9_.]*+';

    /**
     * env variable value pattern
     *
     * @var    string
     */
    const VALUE_PATTERN = '[^\s#\r\n][^#\r\n]*';

    /**
     * {@inheritDoc}
     */
    public static function load(/*# string */ $path)
    {
        // read in file
        @$contents = file_get_contents($path);
        if (false === $contents) {
            throw new NotFoundException(
                Message::get(Message::CONFIG_FILE_NOTFOUND, $path),
                Message::CONFIG_FILE_NOTFOUND
            );
        }

        // set env
        foreach (static::parse($contents) as $name => $val) {
            $val = static::deReference($val);
            putenv("${name}=${val}");
        }
    }

    /**
     * parse and return key/value pairs in array
     *
     * @param  string $contents
     * @return array
     * @access protected
     * @static
     */
    protected static function parse(/*# string */ $contents)/*# : array */
    {
        $result = [];
        $regex = sprintf(
            '/^\s*+(%s)\s*+=\s*+(%s)(?:#.*+)?\s*$/m',
            self::NAME_PATTERN,
            self::VALUE_PATTERN
        );
        if (preg_match_all($regex, $contents, $matched, \PREG_SET_ORDER)) {
            foreach ($matched as $m) {
                $result[$m[1]] = trim($m[2]);
            }
        }
        return $result;
    }

    /**
     * de-referencing env in the value
     *
     * @param  string $value
     * @return string
     * @throws LogicException if error happens
     * @access protected
     * @static
     */
    protected static function deReference(/*# string */ $value)/*# : string */
    {
        $regex = sprintf('/\${(%s)}/', self::NAME_PATTERN);
        while (false !== strpos($value, '${')) {
            $value = preg_replace_callback(
                $regex,
                function ($matched) {
                    $env = static::matchEnv($matched[1]);
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
     * Find the env value from name, support '_SERVER.HTTP_HOST' etc.
     *
     * @param  string $name
     * @return string|false
     * @access protected
     * @static
     */
    protected static function matchEnv(/*# string */ $name)
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
}
