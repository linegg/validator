<?php
/**
 * Validator
 *
 * @author    yuanzhilin
 * @since     2021/12/20
 */

namespace Linegg\Validator;

class Validator
{
    protected $rule;

    protected $errors = [];

    protected $isError = false;

    /**
     * clear error after validate()
     *
     * @var bool
     */
    public $emptyError = true;

    public function setRule($key) : RuleNode{
        if (isset($this->rule[$key])) {
            return $this->rule[$key];
        }

        $this->rule[$key] = new RuleNode();
        return $this->rule[$key];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isError(): bool
    {
        return $this->isError;
    }

    public function clearError() {
        $this->isError = false;
        $this->errors = [];
    }

    /**
     * Core verification method
     *
     * @param mixed $args Variables to be verified
     * @param string $key Key used for single variable validation
     */
    public function validate($args, string $key = '') {
        if ($this->emptyError) {
            $this->clearError();
        }

        if (is_array($args)) {
            foreach ($args as $k => $arg) {
                if (is_array($arg)) {
                    $this->validate($arg);
                } else {
                    // if exist
                    if (isset($this->rule[$k])) {
                        if(!$this->rule[$k]->validate($arg)) {
                            $this->isError = true;
                            $this->errors[$k] = $this->rule[$k]->getErrorMsg();
                        }
                    }
                }
            }
        } else {
            if (!empty($key) && isset($this->rule[$key])) {
                if (!$this->rule[$key]->validate($args)) {
                    $this->isError = true;
                    $this->errors[$key] = $this->rule[$key]->getErrorMsg();
                }
            }
        }
    }
}