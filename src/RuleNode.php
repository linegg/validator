<?php
/**
 * RuleNode
 *
 * @author    yuanzhilin
 * @since     2021/12/20
 */

namespace Linegg\Validator;

use Linegg\Validator\Exception\RuleNodeException;

class RuleNode
{
    protected $maxLength;
    protected $minLength;

    protected $minNumRange = null;
    protected $maxNumRange = null;

    protected $canEmpty = true;
    protected $type;
    protected $regex;

    protected $error = '';

    /**
     * value info
     *
     * @var ValueInfo
     */
    protected $valueInfo;

    static protected $typeList = ['string', 'number', 'boolean'];

    public function getErrorMsg(): string
    {
        return $this->error;
    }

    /**
     * set value length
     *
     * @param integer $min minlength
     * @param integer $max maxlength
     * @return $this
     */
    public function setLength(int $min, int $max): RuleNode
    {
        $this->minLength = $min;
        $this->maxLength = $max;

        return $this;
    }

    /**
     * @return $this
     */
    public function notEmpty(): RuleNode
    {
        $this->canEmpty = false;

        return $this;
    }

    /**
     * @throws RuleNodeException
     */
    public function setType($type): RuleNode
    {
        if (is_array($type)) {
            foreach($type as $v){
                if (!in_array($v, self::$typeList)) {
                    throw new RuleNodeException("{$v} is not an optional value.");
                }
            }

            $this->type = $type;
        } else {
            if (!in_array($type, self::$typeList)) {
                throw new RuleNodeException("{$type} is not an optional value.");
            }

            $this->type = [$type];
        }

        return $this;
    }

    public function setRegex(string $regex): RuleNode {
        $this->regex = $regex;
        return $this;
    }

    public function setNumRange(int $min, int $max): RuleNode {
        $this->minNumRange = $min;
        $this->maxNumRange = $max;

        return $this;
    }

    /**
     * @throws RuleNodeException
     */
    protected function initValueInfo($value) {
        if (!is_array($value) && !is_object($value)) {
            $this->valueInfo = new ValueInfo();
            $this->valueInfo->v_len = strlen($value);
            $this->valueInfo->v_real_type = gettype($value);
            $this->valueInfo->v_raw_value = $value;

            if (is_numeric($value)) {
                $this->valueInfo->v_str_type = 'number';
            } elseif (is_bool($value)) {
                $this->valueInfo->v_str_type = 'boolean';
            } else {
                $this->valueInfo->v_str_type = 'string';
            }
        } else {
            throw new RuleNodeException('value can not be an array or object');
        }
    }

    /**
     * @throws RuleNodeException
     */
    public function validate($value): bool
    {
        $this->initValueInfo($value);
        // type
        if ($this->type) {
            if (!in_array($this->valueInfo->v_str_type, $this->type)) {
                $this->error = 'value type error.';
                return false;
            }
        }

        if (!$this->canEmpty) {
            if ($this->valueInfo->v_str_type != 'number') {
                if (empty($value)) {
                    $this->error = 'value can not be empty';
                    return false;
                }
            }
        }

        if ($this->maxNumRange) {
            if($this->valueInfo->v_str_type == 'number' && $this->maxNumRange < $value){
                $this->error = "value is bigger than {$this->maxNumRange}";
                return false;
            }
        }

        if ($this->minNumRange) {
            if($this->valueInfo->v_str_type == 'number' && $this->minNumRange > $value){
                $this->error = "value is smaller than {$this->minNumRange}";
                return false;
            }
        }

        if ($this->maxLength) {
            if($this->valueInfo->v_len > $this->maxLength){
                $this->error = 'value length is too long.';
                return false;
            }
        }

        if ($this->minLength) {
            if($this->valueInfo->v_len < $this->minLength){
                $this->error = 'value length is too short.';
                return false;
            }
        }

        if ($this->regex) {
            if (!preg_match($this->regex, $this->valueInfo->v_raw_value)) {
                $this->error = 'value can not match regex.';
                return false;
            }
        }

        return true;
    }
}