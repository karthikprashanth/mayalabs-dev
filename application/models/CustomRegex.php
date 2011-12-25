<?php

class Model_CustomRegex extends Zend_Validate_Abstract
{
    const INVALID   = 'regexInvalid';
    const NOT_MATCH = 'regexNotMatch';
    const ERROROUS  = 'regexErrorous';

    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given, value should be string, integer or float",
        self::NOT_MATCH => "'%value%' does not match against pattern '%pattern%'",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    );

    protected $_messageVariables = array(
        'pattern' => '_pattern'
    );

    protected $_pattern;

    public function __construct($pattern)
    {
        if ($pattern instanceof Zend_Config) {
            $pattern = $pattern->toArray();
        }

        if (is_array($pattern)) {
            if (array_key_exists('pattern', $pattern)) {
                $pattern = $pattern['pattern'];
            } else {
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception("Missing option 'pattern'");
            }
        }

        $this->setPattern($pattern);
    }

    public function getPattern()
    {
        return $this->_pattern;
    }

    public function setPattern($pattern)
    {
        $this->_pattern = (string) $pattern;
        $status         = @preg_match($this->_pattern, "Test");

        if (false === $status) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("Internal error while using the pattern '$this->_pattern'");
        }

        return $this;
    }

    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return true;
        }

        $this->_setValue($value);

        $status = @preg_match($this->_pattern, $value);
        if (false === $status) {
            return true;
        }

        if (!$status) {
            return true;
        }
		$this->_error(self::INVALID);
        return false;
    }
}
