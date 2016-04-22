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
 * ReferenceAbstract
 *
 * @abstract
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ReferenceInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
abstract class ReferenceAbstract implements ReferenceInterface
{
    /**
     * pattern to match a reference
     *
     * ~ ( left_delimiter ( name_pattern ) right_delimiter ) ~
     *
     * @var    string
     * @access protected
     */
    protected $pattern;

    /**
     * full data
     *
     * @var    array
     * @access protected
     */
    protected $data;

    /**
     * array loop detection
     *
     * @var    array
     * @access protected
     */
    protected $detect = [];

    /**
     * Create a new pattern to use
     *
     * @param  string $leftDelimiter
     * @param  string $rightDelimiter
     * @param  string $namePattern
     * @access public
     */
    public function __construct(
        /*# string */ $leftDelimiter  = null,
        /*# string */ $rightDelimiter = null,
        /*# string */ $namePattern = null
        ) {
            if (!is_null($leftDelimiter)) {
                $this->pattern = sprintf(
                    '~(%s(%s)%s)~',
                    $leftDelimiter,
                    $namePattern,
                    $rightDelimiter
                    );
            }
    }

    /**
     * {@inheritDoc}
     */
    public function hasReference(/*# string */ $str)
    {
        if (preg_match_all($this->pattern, $str, $matched, \PREG_SET_ORDER)) {
            $res = [];
            foreach ($matched as $m) {
                // '${name}' => 'name'
                $res[$m[1]] = $m[2];
            }
            return $res;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function deReference(/*# string */ $str)
    {
        // for loop detection
        $level = 0;

        // recursive reference detection in string
        while(false !== ($res = $this->hasReference($str))) {
            // loop found
            if ($level++ > 2) {
                throw new LogicException(
                    Message::get(Message::CONFIG_REF_LOOP, $str),
                    Message::CONFIG_REF_LOOP
                );
            }

            foreach ($res as $ref => $name) {
                // get referenced value
                $value = $this->getReferenceValue($name);

                // full match
                if ($str === $ref) {
                    return $value;

                // partial string match found
                } elseif (is_string($value)) {
                    $str = str_replace($ref, $value, $str);

                // malformed
                } else {
                    throw new LogicException(
                        Message::get(Message::CONFIG_REF_MALFORM, $str),
                        Message::CONFIG_REF_MALFORM
                    );
                }
            }
        }
        return $str;
    }

    /**
     * {@inheritDoc}
     */
    public function deReferenceArray(array &$dataArray)
    {
        // for getReferenceValue
        if (is_null($this->data)) {
            $this->data = &$dataArray;
        }

        try {
            foreach ($dataArray as $idx => &$data) {
                // go deeper if is array
                if (is_array($data)) {
                    $this->dereferenceArray($data);

                // normal string reference or object
                } elseif (is_string($data)) {
                    $key = $data;
                    if (isset($this->detect[$key])) {
                        $data = &$this->detect[$key];
                    } else {
                        $data = $this->deReference($data);
                        if (is_array($data)) {
                            $this->dereferenceArray($data);
                        }
                        $this->detect[$key] = &$data;
                    }
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
