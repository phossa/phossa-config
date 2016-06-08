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
 * @version 1.0.6
 * @since   1.0.5 added
 */
interface ReferenceInterface
{
    /**
     * Set reference pattern start and ending string
     *
     * Set $referenceStart to '' to disable reference feature !!
     *
     * @param  string $patternStart, like '${'
     * @param  string|null $patternEnd, like '}'
     * @return self
     * @access public
     */
    public function setReferencePattern(
        /*# : string */ $patternStart,
        $patternEnd
    );

    /**
     * Quick check $string contains reference pattern or not
     *
     * @param  string $string
     * @return bool
     * @access public
     */
    public function hasReference(/*# : string */ $string)/*# : bool */;

    /**
     * Replace all references in the string like '${system.dir}/temp'
     *
     * - recursively dereference a result string
     *
     * - if result is array|object do NOT dereference recursively.
     *
     * @param  string $str
     * @return string|object|array
     * @throws LogicException if malformed reference found
     * @access public
     */
    public function deReference(/*# string */ $str);
}
