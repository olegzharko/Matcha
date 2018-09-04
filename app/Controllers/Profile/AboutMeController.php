<?php
/**
 * Created by PhpStorm.
 * User: ozharko
 * Date: 9/4/18
 * Time: 11:51 AM
 */

namespace Matcha\Controllers\Profile;

use Matcha\Models\User;
use Matcha\Controllers\Controller;
use Respect\Validation\Validator as v;

class AboutMeController extends Controller
{
    public function aboutMe($id, $request)
    {
        $validation = $this->validator->validate($request, [
            'gender' => v::notEmpty(),
            'describe' => v::>notEmpty(),
            
        ]);

        $gender = $request->getParam('gender');

        User::find($_SESSION['user'])->update([
           'gender' =>
        ]);
    }
}