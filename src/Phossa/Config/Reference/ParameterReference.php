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

use Phossa\Config\Message\Message;
use Phossa\Config\Exception\LogicException;

/**
 * ParameterReference
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ReferenceAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class ParameterReference extends ReferenceAbstract
{
    /**
     * default pattern to match a reference
     *
     * ~ ( left_delimiter ( name_pattern ) right_delimiter ) ~
     *
     * @var    string
     * @access protected
     */
    protected $pattern = '~(\${([a-zA-Z_][a-zA-Z0-9._]*+)})~';

    /**
     * Get referenced value by name
     *
     * @param  string $name
     * @return string|array|object
     * @access public
     */
    public function getReferenceValue(/*# string */ $name)
    {
        // super globals start with '_'
        if ('_' === $name[0]) {
            return $this->superGlobalValue($name);

        // normal reference
        } else {
            // $this->data has to be set here
            if (is_null($this->data)) {
                throw new LogicException(
                    Message::get(Message::CONFIG_REF_UNKNOWN, $name),
                    Message::CONFIG_REF_UNKNOWN
                );
            }

            // get my from own reference pool ($this->data)
            return $this->myReferenceValue($name);
        }
    }

    /**
     * Get super global value
     *
     * @param  string $name something like '_SERVER.HTTP_HOST'
     * @return string
     * @throws LogicException if value not found
     * @access protected
     */
    protected function superGlobalValue(/*# string */ $name)/*# : string */
    {
        $pos = strpos($name, '.');
        if (false !== $pos) {
            $pref = substr($name, 0, $pos);
            $suff = substr($name, $pos + 1);
            if (isset($GLOBALS[$pref][$suff])) {
                return $GLOBALS[$pref][$suff];
            }
        }
        throw new LogicException(
            Message::get(Message::CONFIG_REF_UNKNOWN, $name),
            Message::CONFIG_REF_UNKNOWN
        );
    }

    /**
     * Get the reference value
     *
     * @param  string $name
     * @return string|array
     * @throws LogicException
     * @access protected
     */
    protected function myReferenceValue(/*# string */ $name)
    {
        // break into parts by '.'
        $parts = explode('.', $name);

        // data to search thru
        $found = $this->data;
        while (null !== ($part = array_shift($parts))) {
            if (!isset($found[$part])) {
                throw new LogicException(
                    Message::get(Message::CONFIG_REF_UNKNOWN, $name),
                    Message::CONFIG_REF_UNKNOWN
                );
            }
            $found = $found[$part];
        }
        return $found;
    }
}
