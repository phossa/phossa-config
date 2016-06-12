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
 * Dealing with an item pool
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait PoolTrait
{
    /**
     * item pool
     *
     * @var    array
     * @access protected
     */
    protected $pool = [];

    /**
     * Need dereference
     *
     * @var    bool
     * @access protected
     */
    protected $pool_dirty = true;

    /**
     * Is pool dirty
     *
     * @return boolean
     * @access protected
     */
    protected function isPoolDirty()/*# : bool */
    {
        return $this->pool_dirty;
    }

    /**
     * Set pool dirty
     *
     * @param  bool $dirty
     * @return self
     * @access protected
     */
    protected function setPoolDirty(/*# bool */ $dirty = true)
    {
        $this->pool_dirty = $dirty;
    }

    /**
     * clean the pool
     *
     * @return self
     * @access protected
     */
    protected function cleanPool()
    {
        if ($this->isPoolDirty()) {
            $this->cleanDirtyPool();
            $this->setPoolDirty(false);
        }
        return $this;
    }

    /**
     * Real clean method to be implemented
     *
     * @access protected
     */
    abstract protected function cleanDirtyPool();
}
