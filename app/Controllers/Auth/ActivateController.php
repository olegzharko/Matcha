<?php

namespace Matcha\Controllers\Auth;

use Matcha\Controllers\Controller;
use Respect\Validation\Validator as v;
use Matcha\Models\CheckEmail;
use Matcha\Models\User;

class ActivateController extends Controller
{
	protected $uniqid;
	protected $email;
	protected $user;
	protected $validation;

	public function activate($request, $response)
	{
		$validation = $this->validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->email(),
			'uniq_id' => v::noWhitespace()->notEmpty(),
		]);

		if ($validation->failed()) {
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}

		$email = $request->getParam('email');
		$uniqid = $request->getParam('uniq_id');
		$user = CheckEmail::where('uniq_id', $uniqid)->first();

		User::setActiveAccount($user->email);
		DiscoverySettings::createAndSetDefault();
		$email = $request->getParam('email');
		$user = User::where('email', $email)->first();

		if ($user->email_confirmed == 1) {
			$_SESSION['user'] = $user->id;
			CheckEmail::where('uniq_id', $uniqid)->delete();
			return $response->withRedirect($this->router->pathFor('auth.edit.user'));
		}
	}
}
