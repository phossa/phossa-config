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

namespace Phossa\Config\Reference;

/**
 * ReferenceAbstract
 *
 * @abstract
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.0 added
 * @since   1.0.5 added setReferencePattern()
 * @since   1.0.6 moved setReferencePattern() to ReferenceTrait
 */
abstract class ReferenceAbstract implements ReferenceInterface
{
    use TreeTrait, ReferenceTrait;
}
