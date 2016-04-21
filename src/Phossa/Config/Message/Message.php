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

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
        self::CONFIG_FILE_NOTFOUND  => 'File "%s" not found or not readable',
        self::CONFIG_ENV_UNKNOWN    => 'Unknown environment "%s"',
    ];
}
