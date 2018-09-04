<?php

namespace Matcha\Controllers\Profile;

use Matcha\Models\About;
use Matcha\Controllers\Controller;
use Respect\Validation\Validator as v;

class AboutController extends Controller
{
    public function getEditProfile($request, $response)
    {
        return $this->view->render($response, 'user/edit/info.twig');
    }

    public function postEditProfile($request, $response)
    {

        $validation = $this->validator->validate($request, [
            'gender' => v::notEmpty(),
            'aboutme' => v::notEmpty(),
            'sexualPref' => v::notEmpty(),
            'biography' => v::notEmpty(),
//            'listOfInterests' => v::notEmpty(),
//            'photo' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('user.edit.info'));
        }

        About::where('id', $_SESSION['user'])->update([
            'userid' => $_SESSION['user'],
            'gender' => $request->getParam('gender'),
            'aboutme' => $request->getParam('aboutme'),
            'sexualPref' => $request->getParam('sexualPref'),
            'biography' => $request->getParam('biography'),
//            'listOfInterests' => $request->getParam('listOfInterests'),
        ]);

        return $response->withRedirect($this->router->pathFor('home'));
    }
}