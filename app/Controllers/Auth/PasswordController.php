<?php

namespace Matcha\Controllers\Auth;

use Matcha\Models\User;
/*
 * use покажет какой родительский контроллер нужно использовать
 * */
use Matcha\Controllers\Controller;
use Matcha\Controllers\Check\CheckController;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{
    public function getChangePassword($request, $response)
    {
        $userInfo = $this->checker->user();
        $this->container->view->getEnvironment()->addGlobal('email', $userInfo->email);

        return $this->view->render($response, 'user/edit/account-settings.twig');
    }

    public function postChangePassword($request, $response)
    {

        $validation = $this->validator->validate($request, [
            /*
             * password задесь возвращается как свойство
             * которое храниться в объекте User
             * все остальное как метод
             * $this->auth->user()->password
             * 
             * вызовиться метод user() что вернет все что имется в БД
             * из всего этого выбереться только свойство password
             * 
             * с чем он сравнивает? c объектом v:: ?
             * */
           'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
           'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($this->checker->user()->password),
           'password_new' => v::noWhitespace()->notEmpty(),
           'password_repeat' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.password.change'));
        }

        $email = $request->getParam('email');
        $this->checker->user()->setEmail($_SESSION['user'], $email);
        $password1 = $request->getParam('password_new');
        $password2 = $request->getParam('password_repeat');

        if ($this->checker->comparePasswords($password1, $password2, $response)) {
            return $response->withRedirect($this->router->pathFor('auth.password.change'));
        }

        $this->checker->user()->setPassword($request->getParam('password_new'));

        $this->flash->addMessage('info', 'Your password was changed');
        return $response->withRedirect($this->router->pathFor('home'));
    }
}
