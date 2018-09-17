<?php
/**
 * Created by PhpStorm.
 * User: ozharko
 * Date: 8/17/18
 * Time: 2:37 PM
 */

namespace Matcha\Controllers;

use Matcha\Models\CheckEmail;
use Matcha\Models\User;
use Matcha\Models\About;
use Matcha\Models\UserInterest;
use Matcha\Models\Photo;
use Matcha\Controllers\Controller;
use Matcha\Controllers\Check\CheckController;
use Respect\Validation\Validator as v;

class HomeController extends Controller
{
	protected $uniqid;
	protected $email;
	protected $user;
	protected $validation;
	/*
	 * так как мы добавили container, что включает в себя методы twig,
	 * в конструкстор класса Controller мы можем использовать его методы
	 * для отрисовки страниц
	 * */
	public function index($request, $response)
	{
		// $this->flash->addMessage('info', 'Test flash message');
//        $tmp = $this->db->table('users')->find(1);
//        var_dump($tmp->email);
//        die();

//        $user = User::where('email', 'alex@codecourse.com')->first();
//        var_dump($user->email);
//        die();
		$allPhoto = Photo::getUserPhoto();
		$aboutTable = $this->checker->allAboutUser();
		$interestsResult = $this->checker->allValueOfInterests();

		$about['about_me'] = $aboutTable->about_me;
		$about['age'] = $aboutTable->age;
		$about['user_interests'] = $interestsResult;
		if ($allPhoto) {
			$about['user_photo'] = $allPhoto;
		}
		$this->container->view->getEnvironment()->addGlobal('about', $about);
/*
	$data = User::....(); // array   data['name_user']
	// data.name_user
	$this->container->view->getEnvironment()->addGlobal('data', $data);
*/
		return $this->view->render($response, 'home.twig');
	}

	public function hello($request, $response)
	{
		$this->flash->addMessage('info', 'Check you email and confinm your account');
		return $this->view->render($response, 'hello/hello.twig');
	}
}