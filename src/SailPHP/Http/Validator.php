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
class Validator
{

    protected $errors = [];

    public function validate(Request $request, array $rules, $session = true)
    {
        foreach($rules as $field => $rule)
        {
            try {
                $rule->setName($field)->assert($request->get($field));
            } catch(NestedValidationException $e)
            {
                $this->errors[$field] = $e->getMessages();
            }
        }

        if($session) {
            session()->put('_errors', $this->errors);
        }
        return $this;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function failed()
    {
        return !empty($this->errors);
    }
}