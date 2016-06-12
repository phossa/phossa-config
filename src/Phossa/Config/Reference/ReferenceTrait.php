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
 * @version 1.0.6
 * @see     ReferenceInterface
 * @since   1.0.0 added
 * @since   1.0.6 added setReferencePattern() etc.
 * @since   1.0.7 added getReferencePattern()
 */
trait ReferenceTrait
{
    /**
     * start of the reference
     *
     * @var    string
     * @access protected
     */
    protected $reference_start = '${';

    /**
     * end of the reference
     *
     * @var    string
     * @access protected
     */
    protected $reference_end = '}';

    /**
     * pattern to match a reference '${name}'
     *
     * ~ ( left_delimiter ( name_pattern ) right_delimiter ) ~
     *
     * @var    string
     * @access protected
     */
    protected $reference_pattern;

    /**
     * for loop detection
     *
     * @var    array
     * @access protected
     */
    protected $loop_detect = [];

    /**
     * {@inheritDoc}
     */
    public function setReferencePattern(
        /*# : string */ $patternStart,
        $patternEnd
    ) {
        $this->reference_start = $patternStart;
        $this->reference_end = $patternEnd;
        $this->reference_pattern = null;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getReferencePattern()/*# : array */
    {
        return [$this->reference_start, $this->reference_end];
    }

    /**
     * {@inheritDoc}
     */
    public function hasReference(/*# : string */ $string)/*# : bool */
    {
        // non string found
        if (!is_string($string)) {
            return false;
        }

        // disable reference feature by setting pattern start to ''
        if ('' === $this->getPattern()) {
            return false;
        }

        return false !== strpos($string, $this->reference_start);
    }

    /**
     * {@inheritDoc}
     */
    public function deReference(/*# string */ $str)
    {
        // for loop detection in recursive dereference
        $level = 0;

        // unresolved(unknown) reference
        $unresolved = [];

        // find reference in the string RECURSIVELY
        while($this->hasReference($str)) {
            // extract references
            $res = $this->extractReference($str);
            if (empty($res)) {
                break;
            }

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
     * Get the reference pattern
     *
     * @return string
     * @access protected
     */
    protected function getPattern()/*# : string */
    {
        if (is_null($this->reference_pattern)) {
            if ('' === $this->reference_start ||
                is_null($this->reference_end)) {
                $this->reference_pattern = '';
            } else {
                $this->reference_pattern = '~('  .
                    preg_quote($this->reference_start) .
                    '([a-zA-Z_][a-zA-Z0-9._]*+)' .
                    preg_quote($this->reference_end) . ')~';
            }
        }
        return $this->reference_pattern;
    }

    /**
     * Extract reference from $str, such as '${name}xxXX'.
     *
     * Returns ['${name}' => 'name'] if found, or empty array if not found
     *
     * @param  string $str
     * @return array
     * @access protected
     */
    protected function extractReference(/*# string */ $str)/*# : array */
    {
        $res = [];
        if (preg_match_all(
            $this->getPattern(),
            $str,
            $matched,
            \PREG_SET_ORDER
        )) {
            foreach ($matched as $m) {
                $res[$m[1]] = $m[2];
            }
            return $res;
        }
        return $res;
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
        // reference feature disabled
        if ('' === $this->getPattern()) {
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
     * Get raw value (not dereferenced) by name
     *
     * @param  string $name
     * @return null|string|array|object
     * @access protected
     */
    abstract protected function getValue(/*# string */ $name);
}
