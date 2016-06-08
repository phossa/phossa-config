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
 * ReferenceInterface
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.5
 * @since   1.0.5 added
 */
interface ReferenceInterface
{
    /**
     * Set reference pattern
     *
     * @param  string $pattern
     * @return self
     * @access public
     */
    public function setReferencePattern(/*# : string */ $pattern);
}
