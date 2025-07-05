<?php
/**
 * Class Validate
 *
 * This class handles validation of user input based on a set of defined rules.
 * It supports rules like 'required', 'min', 'max', 'matches', and 'unique'.
 * Errors are collected during the validation process and can be accessed afterward.
 *
 * Properties:
 * - $_passed: Indicates if all validations passed.
 * - $_errors: Stores validation error messages.
 * - $_db: Holds the database instance for rules like 'unique'.
 *
 * Methods:
 * - check($source, $items): Validates input data based on the rules provided.
 * - addError($error): Adds an error message to the list.
 * - errors(): Returns all collected error messages.
 * - passed(): Returns whether validation passed.
 */
class Validate {
    private $_passed = false,
            $_errors = array(),
            $_db = null;

    /**
     * Constructor: Initializes the database instance.
     */
    public function __construct() {
        $this->_db = DB::getInstance();
    }

    /**
     * Perform validation on given data source.
     *
     * @param array $source The source data (e.g., $_POST).
     * @param array $items An associative array of items and their validation rules.
     * @return $this Returns the current instance.
     */
    public function check($source, $items = array()) {
        foreach ($items as $item => $rules) {
            foreach ($rules as $rule => $rule_value) {
                $value = trim($source[$item]);
                $item = htmlspecialchars($item);

                if ($rule === 'required' && empty($value)) {
                    $this->addError("{$item} is required");
                } else if (!empty($value)) {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters");
                            }
                            break;
                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters");
                            }
                            break;
                        case 'matches':
                            if ($value != $source[$rule_value]) {
                                $this->addError("{$rule_value} must match {$item}");
                            }
                            break;
                        case 'unique':
                            $check = $this->_db->get($rule_value, array($item, '=', $value));
                            if ($check->count()) {
                                $this->addError("{$item} already exists.");
                            }
                            break;
                    }
                }
            }
        }

        if (empty($this->_errors)) {
            $this->_passed = true;
        }

        return $this;
    }

    /**
     * Add an error message to the errors array.
     *
     * @param string $error The error message to add.
     */
    private function addError($error) {
        $this->_errors[] = $error;
    }

    /**
     * Get all collected error messages.
     *
     * @return array The list of error messages.
     */
    public function errors() {
        return $this->_errors;
    }

    /**
     * Check if validation passed.
     *
     * @return bool True if passed, false otherwise.
     */
    public function passed() {
        return $this->_passed;
    }
}
