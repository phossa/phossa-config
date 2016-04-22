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
    ];
}
