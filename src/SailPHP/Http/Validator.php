<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/04/2019
 * Time: 2:04 PM
 */

namespace SailPHP\Http;

use Respect\Validation\Validator as Resepect;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Class Validator
 * @package SailPHP\Http
 */
class Validator
{

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param Request $request
     * @param array $rules
     * @param bool $session
     * @return $this
     */
    public function validate(Request $request, array $rules, $session = true)
    {   
        foreach($rules as $field => $rule)
        {
            if(is_string($rule)) {
                $rule = explode('|', $rule);
            }
            
            if(is_array($rule)) {
                $validator = Resepect::create();
                // add rules
                foreach($rule as $r) {
                    try {
                        $validator = $validator->{$r}();
                    } catch(\Exception $e) {
                        throw new \Exception("Invalid validation rule: {$r}");
                    }
                }

                // validate
                try {
                    $validator->setName(ucfirst($field))->assert($request->get($field));
                } catch(NestedValidationException $e) {
                    $this->errors[$field] = $e->getMessages();
                }
            } else {

                try {
                    $rule->setName($field)->assert($request->get($field));
                } catch(NestedValidationException $e)
                {
                    $this->errors[$field] = $e->getMessages();
                }
            }
        }

        if($session) {
            session()->put('_errors', $this->errors);
        }
        
        return $this;
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function failed()
    {
        return !empty($this->errors);
    }
}
