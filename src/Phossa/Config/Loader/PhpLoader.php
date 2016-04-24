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

use Phossa\Config\Exception\LogicException;
use Phossa\Config\Message\Message;

/**
 * PHP file loader
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     FileLoaderInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class PhpLoader implements FileLoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function load(/*# string */ $path)/*# : array */
    {
        try {
            $data = include $path;
        } catch (\Exception $exception) {
            throw new LogicException(
                Message::get(Message::CONFIG_LOAD_ERROR, $path),
                Message::CONFIG_LOAD_ERROR,
                $exception
            );
        }

        // callable
        if (is_callable($data)) {
            $data = $data();
        }

        if (!is_array($data)) {
            throw new LogicException(
                Message::get(Message::CONFIG_FORMAT_ERROR),
                Message::CONFIG_FORMAT_ERROR
            );
        }

        return $data;
    }
}
