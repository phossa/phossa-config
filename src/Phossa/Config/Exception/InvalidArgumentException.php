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

namespace Phossa\Config\Exception;

use Phossa\Shared\Exception\InvalidArgumentException as IAException;

/**
 * InvalidArgumentException
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExceptionInterface
 * @see     \Phossa\Shared\Exception\InvalidArgumentException
 * @version 1.0.0
 * @since   1.0.0 added
 */
class InvalidArgumentException extends IAException implements ExceptionInterface
{
}
