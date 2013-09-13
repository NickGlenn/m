<?php

namespace m\Validation;
use m\Http\SessionInterface;

class Validator 
{

    /**
     * @var array Error log
     */
    protected $_errors = array();

    /**
     * @var array Error messages
     */
    protected $_messages = array(
        'csrf'      => 'CSRF Token Mismatch!  Validation failed.',
        'required'  => 'The :key field is required.'
    );

    /**
     * @var array The current rules to check against
     */
    protected $_rules = array();

    /**
     * @var \m\Http\SessionInterface Session object
     */
    protected $_session;

    /**
     * @var array The validation methods
     */
    protected static $_handlers = array(
        'min'       => 'm\Validation\Handlers\MinimumHandler',
        'max'       => 'm\Validation\Handlers\MaximumHandler',
        'between'   => 'm\Validation\Handlers\BetweenHandler',
        'before'    => 'm\Validation\Handlers\BeforeHandler',
        'after'     => 'm\Validation\Handlers\AfterHandler',
        'email'     => 'm\Validation\Handlers\EmailHandler'
    );

    /**
     * Constructor.
     *
     * @param array $rules
     */
    public function __construct(array $rules = array())
    {
        $this->_rules = $rules;
    }

    /**
     * Sets a handler.
     *
     * @param string $key
     * @param \m\Validation\HandlerAbstract $handler
     */
    public static function setHandler($key, HandlerAbstract $handler)
    {
        static::$_handlers[$key] = $handler;
    }

    /**
     * Returns a handler if it exists.
     *
     * @param string $key
     * @return object|null
     */
    public static function getHandler($key)
    {
        return isset(static::$_handlers[$key]) ? static::$_handlers[$key] : null;
    }

    /**
     * Returns true if handler exists.
     *
     * @param string $key
     */
    public static function hasHandler($key)
    {
        return isset(static::$_handlers);
    }

    /**
     * Clears a handler.
     *
     * @param string $key
     */
    public static function clearHandler($key)
    {
        unset(static::$_handlers[$key]);
    }

    /**
     * Returns an array of available handler keys.
     *
     * @return array
     */
    public static function getHandlerKeys()
    {
        return array_keys(static::$_handlers);
    }

    /**
     * Set a session object for the CSRF token.
     *
     * @param \m\Http\SessionInterface $session
     * @return \m\Validation\Validator
     */
    public function setSession(SessionInterface $session)
    {
        $this->_session = $session;

        return $this;
    }

    /**
     * Return the stored session object.
     *
     * @return \m\Http\SessionInterface
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Sets the default messages.
     *
     * @param array $messages
     * @return \m\Validation\Validator
     */
    public function setMessages(array $messages)
    {
        $this->_messages = $messages;

        return $this;
    }

    /**
     * Returns all of the default messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Set rules for an item key.
     *
     * @param string $key
     * @param string|array $rules
     * @return \m\Validation\Validator
     */
    public function setRulesFor($key, $rules)
    {
        if (is_array($rules) || is_string($rules))
            $this->_rules[$key] = $rules;

        return $this;
    }

    /**
     * Gets the rules for an item, if it is set.
     *
     * @param string $key
     * @return array|null
     */
    public function getRulesFor($key)
    {
        if (!isset($this->_rules[$key]))
            return null;

        if (is_string($this->_rules[$key]))
            return explode('|', $this->_rules[$key]);

        return $this->_rules[$key];
    }

    /**
     * Returns true if an item matching the key exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasRulesFor($key)
    {
        return isset($this->_rules[$key]);
    }

    /**
     * Clear the rules for an item.
     *
     * @param string $key
     * @return \m\Validation\Validator
     */
    public function clearRulesFor($key)
    {
        unset($this->_rules[$key]);

        return $this;
    }

    /**
     * Checks the given data against the set rules.
     *
     * @param array $data
     * @param array $messages
     * @return bool
     */
    public function check(array $data, array $messages = array())
    {

        // Convert the given data to an object
        $data = (object) $data;

        // Merge the given messages with the set messages.
        $messages = array_merge($this->_messages, $messages);

        // Check the CSRF token if we have a session object
        if ($this->_session) {

            if (!isset($data->csrf_token) || $data->csrf_token !== $this->_session->getToken()) {

                $this->_errors[] = $messages['csrf'];
                return false;

            }

        }

        // Loop all the set rules and validate
        foreach ($this->_rules as $key => $rules)
        {
            // If the rules are a string, break it apart
            if (is_string($rules))
                $rules = explode('|', $rules);

            // Get the data for the rule
            $input = isset($data->$key) ? $data->$key : null;

            // Get the type of the input
            $inputType = $this->getInputType($input);

            // Create a key for messages
            $m_key = str_replace(array('_', '-'), ' ', $key);

            // Loop through each rule and validate
            foreach($rules as $rule) {

                // Turn the rule into an array and explode if it has parameters
                $rule = strpos($rule, ':') !== false ? explode(':', $rule) : array($rule);

                // If the rule is "required", it's a built in rule
                if ($rule[0] == 'required') {

                    if (!$input || empty($input)) {

                        if (isset($messages['required.'.$key])) {
                            $message = $messages['required.'.$key];
                        } else {
                            $message = str_replace(':key', $m_key, $messages['required']);
                        }

                        $this->_errors[] = $message;

                        break;

                    }

                    continue;

                }

                // If it's not a required field we have been given null, skip it
                if (!$input || empty($input))
                    break;

                // Throw an error if we don't have a handler for the rule
                if (!isset(static::$_handlers[$rule[0]]))
                    continue;
                //throw new \Exception('m\Validator: "'.$rule[0].'" is not a valid rule.  No matching handler exists.');

                // Handle lazy loaded handlers
                if (!is_object(static::$_handlers[$rule[0]])) {
                    $handlerName = static::$_handlers[$rule[0]];
                    $handler = new $handlerName();
                    static::$_handlers[$rule[0]] = $handler;
                } else {
                    $handler = static::$_handlers[$rule[0]];
                }

                // If there are parameters, turn them into an array
                $params = (isset($rule[1])) ? explode(',', $rule[1]) : array();

                // Add the input field to the front of the params array
                array_unshift($params, $input);

                // Call the handler and pass it the parameters
                $result = call_user_func_array(array($handler, 'check'.ucfirst($inputType)), $params);

                // If the check failed, log an error and stop validating this field
                if ($result === false) {

                    // Shorthand message keys
                    $ruleFieldName      = $rule[0].'.'.$key;
                    $ruleTypeName       = $rule[0].'.'.$inputType;

                    // Determine which message is available to send
                    if (isset($messages[$ruleFieldName])) {
                        $message = $messages[$ruleFieldName];
                    } elseif (isset($messages[$ruleTypeName])) {
                        $message = $messages[$ruleTypeName];
                    } elseif (isset($messages[$rule[0]])) {
                        $message = $messages[$rule[0]];
                    } else {
                        $message = 'The :key field failed to validate.';
                    }

                    array_shift($params);

                    // Format and log the message
                    $this->_errors[] = $handler->formatMessage($message, $m_key, $params);

                    // Onto the next field
                    break;
                }
            }
        }

        // If we have errors, we failed...
        return count($this->_errors) > 0 ? false : true;
    }

    /**
     * Checks the type of the input and returns a validation type string
     * of "string", "numeric", "array" or "file".  Will return null if a
     * non-validation type is found.
     *
     * @param mixed $input
     * @return string|null
     */
    protected function getInputType($input)
    {
        // Get the formal type
        $type = gettype($input);

        // Check if it's numeric
        if ($type == 'integer' || $type == 'float' || $type == 'double')
            $type = 'numeric';

        // If it's something we don't check, null it
        if ($type == 'NULL' || $type == 'object' || $type == 'resource' || $type == 'undefined type')
            $type = null;

        // Check if it's an file array
        if ($type == 'array' && isset($input['type']) && $input['tmp_name'])
            $type = 'file';

        return $type;
    }

    /**
     * Returns any stored error messages as an object if the
     * object argument is true or as an array if the object
     * argument is false.
     *
     * @param bool $object
     * @return array|object
     */
    public function errors($object = true)
    {
        return $object ? (object) $this->_errors : $this->_errors;
    }

    /**
     * Clears any stored error messages.
     *
     * @return \m\Validation\Validator
     */
    public function clearErrors()
    {
        $this->_errors = array();

        return $this;
    }

}