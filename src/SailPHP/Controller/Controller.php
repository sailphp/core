<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 12:46 PM
 */

namespace SailPHP\Controller;

abstract class Controller
{
    public function validate($request, $rules)
    {
        $validator = container('validator')->validate($request, $rules);

        if ($validator->failed()) {
            response()->json([
                'errors' => $validator->errors()
            ], 422);
            die();
        }

        return true;
    }
}
