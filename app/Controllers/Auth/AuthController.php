<?php

namespace Matcha\Controllers\Auth;

use Matcha\Models\CheckEmail;
use Matcha\Models\User;
use Matcha\Models\About;

/* use покажет какой родительский контроллер нужно использовать
 * */
use Matcha\Controllers\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
	public function getSignIn($request, $response)
	{
		return $this->view->render($response, 'auth/signin.twig');
	}

	public function postSignIn($request, $response)
	{
		$validation = $this->validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->email(),
			'password' => v::noWhitespace()->notEmpty(),
		]);

		if ($validation->failed()) {
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}
		/* вызываем функцию attemp() что в class Auth
		 * и передаем уже в нее все переменные что поступили с post
		 * которые можно вытянуть через getParam 
		 * */
		$user = User::where('email', $request->getParam('email'))->first();

		if ($user->active === 0) {
			$this->flash->addMessage('error', 'Please finish your registration. Check your mail box.');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}

		$auth = $this->checker->attempt(
			$request->getParam('email'),
			$request->getParam('password')
		);

		if (!$auth) {
			$this->flash->addMessage('error', 'Email or password is incorrect');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignUp($request, $response)
	{
		/* функция для отображения содержимого
		 * */
		return $this->view->render($response, 'auth/signup.twig');
	}

	public function postSignUp($request, $response)
	{
		/* в calss Validator было добавлено библиотеку Respect/Validation
		 * мы используем все статические методы данной библиотеки
		 * для проверки обробаываемых данных
		 * */
		$validation = $this->validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
			'username' => v::notEmpty()->usernameAvailable(),
			'name' => v::notEmpty()->alpha(),
			'surname' => v::notEmpty()->alpha(),
			'password' => v::noWhitespace()->notEmpty(),
			'password_repeat' => v::noWhitespace()->notEmpty(),
		]);

		if ($validation->failed()) {
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}

		$password1 = $request->getParam('password');
		$password2 = $request->getParam('password_repeat');

		if (!preg_match("/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $password1)) {
			$this->flash->addMessage('error', 'Password must be at least 8 characters and must contain at least one lower case letter, one upper case letter and one digit');
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}
		if ($this->checker->comparePasswords($password1, $password2, $response)) {
			$this->flash->addMessage('error', 'Passwords does not match');
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}

	   /* в calss Validator было добавлено библиотеку Respect/Validation
		* мы используем все статические методы данной библиотеки
		* для проверки обробаываемых данных
		* */
		$user = User::create([
			'email' => $request->getParam('email'),
			'username' => $request->getParam('username'),
			'name' => $request->getParam('name'),
			'surname' => $request->getParam('surname'),
			'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
		]);

		About::create([
			'user_id' => $user->id,
		]);

		$checkEmail = CheckEmail::create([
			'email' => $user->email,
			'uniq_id' => md5(uniqid(rand(),time())),
		]);

		/* так как человек что регестрируется по идеи должен автоматически зайти на сайт
		 * если он прошел всю верификацию
		 * то введенные им данные следует передать в функцию входа юзера
		 * $user->email с вернувшигося объекта
		 * $request->getParam('password') пароль с вытягиваем с запроса
		 * */
		/*$this->auth->attempt($user->email, $request->getParam('password'));*/

		/* так как мы перенапрявлялись автоматически на главную / стартовую страницу
		 * нам надо указать точно куда надо перейти
		 * */
		$this->sendEmail->sendEmail($user->email, $user->username, $checkEmail->uniq_id);
		$this->flash->addMessage('global', 'Please check your email to confirm regestration');
		return $response->withRedirect($this->router->pathFor('auth.signup'));
	}

	public function getResetPassword($request, $response)
	{
		return $this->view->render($response, 'auth/forgot-passwd.twig');
	}

	public function postResetPassword($request, $response)
	{
		$validation = $this->validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->email(),
		]);
		if ($validation->failed()) {
			return $response->withRedirect($this->router->pathFor('auth.password.forgot'));
		}

		$user = User::where('email', $request->getParam('email'))->first();
		if (!$user) {
			$this->flash->addMessage('error', 'Can\'t find that email, sorry');
			return $response->withRedirect($this->router->pathFor('auth.password.forgot'));
		}

		$new_password = $this->checker->random_str(10);
		$user->setPassword($new_password);
		$this->sendEmail->sendNewPasswordEmail($user->email, $user->username, $new_password);
		$this->flash->addMessage('global', 'Check your email for a link to reset your password. If it doesn’t appear within a few minutes, check your spam folder.');
		return $response->withRedirect($this->router->pathFor('auth.password.forgot'));
	}

	public function getSignOut($request, $response)
	{
		$this->checker->logout();
		return $response->withRedirect($this->router->pathFor('auth.signin'));
	}
}
