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
 * PHP INI file loader
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     LoaderInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class IniLoader implements LoaderInterface
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

        try {
            $data = @parse_ini_file($path, true);
        } catch (\Exception $exception) {
            throw new LogicException(
                Message::get(Message::CONFIG_FORMAT_ERROR),
                Message::CONFIG_FORMAT_ERROR
            );
        }

        if (!$data) {
            $error = error_get_last();
            throw new LogicException(
                Message::get($error['message']),
                Message::CONFIG_FORMAT_ERROR
            );
        }

        return $data;
    }
}
