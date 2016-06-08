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

namespace Phossa\Config;

use Phossa\Config\Message\Message;
use Phossa\Config\Reference\PoolTrait;
use Phossa\Config\Reference\ReferenceAbstract;
use Phossa\Config\Exception\InvalidArgumentException;

/**
 * Parameter
 *
 * Working with an array of parameters with possible parameter references.
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ReferenceAbstract
 * @see     ConfigInterface
 * @version 1.0.6
 * @since   1.0.0 added
 * @since   1.0.6 changed constructor parameters
 */
class Parameter extends ReferenceAbstract implements ConfigInterface
{
    use PoolTrait;

    /**
     * Set the whole parameter pool
     *
     * @param  array $parameters
     * @access public
     */
    public function __construct(array $parameters = []) {

        // set parameter pool, NOTHING is dereferenced here
        $this->set(null, $parameters);
    }

    /**
     * {@heritDoc}
     */
    public function get($key, $default = null)
    {
        // dereference if pool is dirty
        $this->cleanPool();

        // get the whole pool
        if (null === $key) {
            return $this->pool;

        // get by $key
        } else {
            $this->exceptionIfNotString($key);
            $res = $this->getValue($key);

            // return default value if null found
            return null === $res ? $default : $res;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        // set the whole pool
        if (null === $key) {
            // value has to be array
            if (!is_array($value)) {
                throw new InvalidArgumentException(
                    Message::get(Message::CONFIG_VALUE_INVALID),
                    Message::CONFIG_VALUE_INVALID
                );
            }
            $this->pool = $this->fixValue($value);

        // set a key/value pair
        } else {
            $this->exceptionIfNotString($key);

            // search tree, create node if not exist
            $found = &$this->searchTree(
                $key, $this->pool, true
            );

            // fix if $value is array
            if (is_array($value)) {
                $value = $this->fixValue($value);
            }
            $found = $value;
        }

        // new data added, so mark pool dirty
        $this->setPoolDirty();

        return $this;
    }

    /**
     * {@heritDoc}
     */
    public function has(/*# string */ $key)
    {
        return null !== $this->get((string) $key);
    }

    /**
     * Implement PoolTrait::cleanDirtyPool()
     *
     * {@inheritDoc}
     */
    protected function cleanDirtyPool()
    {
        // clear cache and dereference all
        $this->deReferenceArray($this->pool, true);
    }

    /**
     * Implement ReferenceTrait::getValue().
     *
     * - check super globals if name like '_SERVER.HTTP_HOST'
     *
     * - search the parameter pool to find the right value
     *
     * {@inheritDoc}
     */
    protected function getValue(/*# string */ $name)
    {
        // get '${_SERVER.HTTP_HOST}' etc.
        if ('_' === $name[0]) {
            return $this->getSuperGlobalValue($name);

        // get raw value of $name
        } else {
            return $this->searchTree($name, $this->pool);
        }
    }

    /**
     * Get super global value
     *
     * @param  string $name something like '_SERVER.HTTP_HOST'
     * @return string|array
     * @access protected
     */
    protected function getSuperGlobalValue(/*# string */ $name)
    {
        $pos = strpos($name, $this->field_splitter);
        if (false !== $pos) {
            $pref = substr($name, 0, $pos);
            $suff = substr($name, $pos + 1);
            if (isset($GLOBALS[$pref][$suff])) {
                return $GLOBALS[$pref][$suff];
            }
        } else {
            $pref = $name;
            if (isset($GLOBALS[$pref])) {
                return $GLOBALS[$pref];
            }
        }
        return null;
    }

    /**
     * Throw exception if not a string
     *
     * @param  string $key
     * @throws InvalidArgumentException if $key not a string
     * @access public
     */
    protected function exceptionIfNotString($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(
                Message::get(Message::CONFIG_KEY_INVALID, $key),
                Message::CONFIG_KEY_INVALID
            );
        }
    }
}
