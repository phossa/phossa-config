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
 * @version 1.0.0
 * @since   1.0.0 added
 */
abstract class ReferenceAbstract
{
    use TreeTrait, ReferenceTrait;

    /**
     * Constructor, reset reference pattern if you want to
     *
     * @param  string $referencePattern
     * @access public
     */
    public function __construct(
        /*# string */ $referencePattern = null
    ) {
        if (!is_null($referencePattern)) {
            $this->reference_pattern = $referencePattern;
        }
    }
}
