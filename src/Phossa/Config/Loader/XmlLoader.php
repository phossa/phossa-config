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
 * XML file loader
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     LoaderInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class XmlLoader implements LoaderInterface
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

        libxml_use_internal_errors(true);

        $data = simplexml_load_file($path, null, \LIBXML_NOERROR);

        if (false === $data) {
            $errors   = libxml_get_errors();
            $error    = array_pop($errors);
            $message  = $error->message;
            throw new LogicException(
                $message,
                Message::CONFIG_FORMAT_ERROR
            );
        }

        $data = json_decode(json_encode($data), true);

        return $data;
    }
}
