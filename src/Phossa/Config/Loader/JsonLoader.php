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
use Phossa\Config\Exception\LogicException;

/**
 * JSON file loader
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     FileLoaderInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class JsonLoader implements FileLoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function load(/*# string */ $path)/*# : array */
    {
        if (!is_readable($path)) {
            throw new LogicException(
                Message::get(Message::CONFIG_LOAD_ERROR, $path),
                Message::CONFIG_LOAD_ERROR
                );
        }

        $data = @json_decode(file_get_contents($path), true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            if (function_exists('json_last_error_msg')) {
                $message = json_last_error_msg();
            } else {
                $message = Message::get(Message::CONFIG_FORMAT_ERROR);
            }
            throw new LogicException(
                $message,
                Message::CONFIG_FORMAT_ERROR
            );
        }

        return $data;
    }
}
