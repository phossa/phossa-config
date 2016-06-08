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
 * @version 1.0.5
 * @since   1.0.0 added
 * @since   1.0.5 added setReferencePattern()
 */
abstract class ReferenceAbstract implements ReferenceInterface
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
            $this->setReferencePattern($referencePattern);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setReferencePattern(/*# : string */ $pattern)
    {
        $this->reference_pattern = $pattern;
        return $this;
    }
}
