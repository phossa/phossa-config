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

namespace Phossa\Config\Message;

use Phossa\Shared\Message\MessageAbstract;

/**
 * Message class for Phossa\Config
 *
 * @package \Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     MessageAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Message extends MessageAbstract
{
    /**#@+
     * @var   int
     */

    /**
     * File "%s" not found or not readable
     */
    const CONFIG_FILE_NOTFOUND      = 1604211340;

    /**
     * Unknown environment "%s"
     */
    const CONFIG_ENV_UNKNOWN        = 1604211341;

    /**
     * Load config file "%s" error
     */
    const CONFIG_LOAD_ERROR         = 1604211342;


    /**
     * Config content is not array
     */
    const CONFIG_FORMAT_ERROR       = 1604211343;

    /**
     * Config file suffix "%s" unknown
     */
    const CONFIG_SUFFIX_UNKNOWN     = 1604211344;

    /**
     * Malformed reference found in "%s"
     */
    const CONFIG_REF_MALFORM        = 1604211345;

    /**
     * Reference loop found in "%s"
     */
    const CONFIG_REF_LOOP           = 1604211346;

    /**
     * Reference name "%s" unknown
     */
    const CONFIG_REF_UNKNOWN        = 1604211347;

    /**
     * Config key "%s" is not a string
     */
    const CONFIG_KEY_INVALID        = 1604211348;

    /**
     * Dir "%s" is nonexist or not readable
     */
    const CONFIG_DIR_INVALID        = 1604211349;

    /**
     * Invalid value to set
     */
    const CONFIG_VALUE_INVALID      = 1604211350;

    /**
     * Invalid cache directory "%s"
     */
    const CACHE_DIR_INVALID         = 1604211351;

    /**
     * Cache directory "%s" not writable
     */
    const CACHE_DIR_NONWRITABLE     = 1604211352;

    /**
     * Config cache not set yet
     */
    const CACHE_NOT_READY           = 1604211353;

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
        self::CONFIG_FILE_NOTFOUND  => 'File "%s" not found or not readable',
        self::CONFIG_ENV_UNKNOWN    => 'Unknown environment "%s"',
        self::CONFIG_LOAD_ERROR     => 'Load config file "%s" error',
        self::CONFIG_FORMAT_ERROR   => 'Config content is not array',
        self::CONFIG_SUFFIX_UNKNOWN => 'Config file suffix "%s" unknown',
        self::CONFIG_REF_MALFORM    => 'Malformed reference found in "%s"',
        self::CONFIG_REF_LOOP       => 'Reference loop found in "%s"',
        self::CONFIG_REF_UNKNOWN    => 'Reference name "%s" unknown',
        self::CONFIG_KEY_INVALID    => 'Config key "%s" is not a string',
        self::CONFIG_DIR_INVALID    => 'Dir "%s" is nonexist or not readable',
        self::CONFIG_VALUE_INVALID  => 'Invalid value to set',
        self::CACHE_DIR_INVALID     => 'Invalid cache directory "%s"',
        self::CACHE_DIR_NONWRITABLE => 'Cache directory "%s" not writable',
        self::CACHE_NOT_READY       => 'Config cache not set yet',
    ];
}
