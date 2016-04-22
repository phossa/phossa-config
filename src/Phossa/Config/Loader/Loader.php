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
 * Config file loader
 *
 * @package Phossa\PACKAGE
 * @author  Hong Zhang <phossa@126.com>
 * @see     LoaderInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Loader implements LoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function load(/*# string */ $path)/*# : array */
    {
        // find file suffix
        $suff = @array_pop(explode('.', strtolower(basename($path))));
        if (is_string($suff)) {
            $class = __NAMESPACE__ . '\\' . ucfirst($suff) . 'Loader';
            if (!class_exists($class, true)) {
                unset($class);
            }
        }

        // unknown suffix
        if (!isset($class)) {
            throw new LogicException(
                Message::get(Message::CONFIG_SUFFIX_UNKNOWN, $path),
                Message::CONFIG_SUFFIX_UNKNOWN
            );
        }

        return $class::load($path);
    }
}
