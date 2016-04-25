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
 * ReferenceTrait
 *
 * @package Phossa\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait ReferenceTrait
{
    /**
     * pattern to match a reference '${name}'
     *
     * ~ ( left_delimiter ( name_pattern ) right_delimiter ) ~
     *
     * @var    string
     * @access protected
     */
    protected $reference_pattern = '~(\${([a-zA-Z_][a-zA-Z0-9._]*+)})~';

    /**
     * for loop detection
     *
     * @var    array
     * @access protected
     */
    protected $loop_detect = [];

    /**
     * Verify $str has reference¡¡('${name}xxXX') in it.
     *
     * Returns ['${name}' => 'name'] if found, or FALSE if not found
     *
     * @param  string $str
     * @return false|array
     * @access protected
     */
    protected function hasReference(/*# string */ $str)
    {
        // disable reference feature by setting pattern to ''
        if ('' === $this->reference_pattern) {
            return false;
        }

        if (preg_match_all(
            $this->reference_pattern,
            $str,
            $matched,
            \PREG_SET_ORDER
        )) {
            $res = [];
            foreach ($matched as $m) {
                $res[$m[1]] = $m[2];
            }
            return $res;
        }
        return false;
    }

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
     * @access protected
     */
    protected function deReference(/*# string */ $str)
    {
        // disable reference feature by setting pattern to ''
        if ('' === $this->reference_pattern) {
            return $str;
        }

        // for loop detection in recursive dereference
        $level = 0;

        // unresolved(unknown) reference
        $unresolved = [];

        // find reference in the string RECURSIVELY
        while(false !== ($res = $this->hasReference($str))) {
            // loop found
            if ($level++ > 10) {
                throw new LogicException(
                    Message::get(Message::CONFIG_REF_LOOP, $str),
                    Message::CONFIG_REF_LOOP
                );
            }

            // all unresolved
            if ($res == $unresolved) {
                break;
            }

            foreach ($res as $ref => $name) {

                if (!isset($unresolved[$ref])) {
                    $value = $this->getValue($name);
                } else {
                    continue;
                }

                // try resolve
                if (is_null($value)) {
                    $value = $this->resolveUnResolved($name);
                }

                // unresolved
                if (is_null($value)) {
                    $unresolved[$ref] = $name;

                // value is string
                } elseif (is_string($value)) {
                    $str = str_replace($ref, $value, $str);

                // value is array or object
                } elseif ($str === $ref) {
                    return $value;

                // malformed, array|object + string found
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
     * Derefence all references in an array
     *
     * @param  array &$dataArray
     * @param  bool $clearCache clear dereference cache
     * @throws LogicException if malformed reference found
     * @access protected
     */
    protected function deReferenceArray(
        array &$dataArray,
        /*# bool */ $clearCache = false
    ) {
        // disable reference feature by setting pattern to ''
        if ('' === $this->reference_pattern) {
            return;
        }

        // clear old cache
        if ($clearCache) {
            $this->loop_detect = [];
        }

        try {
            foreach ($dataArray as $idx => &$data) {
                // go deeper if is array
                if (is_array($data)) {
                    $this->dereferenceArray($data);

                // $data is string
                } elseif (is_string($data)) {
                    $key = $data;
                    if (isset($this->loop_detect[$key])) {
                        $data = $this->loop_detect[$key];
                    } else {
                        $data = $this->deReference($data);
                        if (is_array($data)) {
                            $this->dereferenceArray($data);
                        }
                        $this->loop_detect[$key] = $data;
                    }
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * If unresolved reference found, try this
     *
     * @param  string $name
     * @return null|string|array
     * @access protected
     */
    protected function resolveUnResolved(/*# : string */ $name)
    {
        return null;
    }

    /**
     * Get raw value (not dereferenced£© by name
     *
     * @param  string $name
     * @return null|string|array|object
     * @access protected
     */
    abstract protected function getValue(/*# string */ $name);
}
