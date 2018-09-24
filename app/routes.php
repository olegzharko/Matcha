<?php
use Matcha\Middleware\AuthMiddleware;
use Matcha\Middleware\GuestMiddleware;
/*
 * так как мы добавили контроллер HomeController в container (app.php)
 * мы можем ипользовать его методы чере одинарное двоиточие
 */
$app->group('', function () {
	/*
	 * setName связан, с twig файлами path_for и return $response->withRedirect($this->router->path('home'));
	 * */
	$this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
	$this->post('/auth/signup', 'AuthController:postSignUp');

	$this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
	$this->post('/auth/signin', 'AuthController:postSignIn');
	$this->get('/hello', 'HomeController:hello')->setName('hello');

	$this->post('/activate', 'ActivateController:activate');
})->add(new GuestMiddleware($container));

$app->group('', function () {
	$this->get('/', 'HomeController:index')->setName('home');
	
	$this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

	$this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/auth/password/change', 'PasswordController:postChangePassword');

	$this->get('/auth/edit/user', 'EditController:getChangeProfile')->setName('auth.edit.user');
	$this->post('/auth/edit/user', 'EditController:postChangeProfile');

	$this->get('/user/edit/info', 'AboutController:getEditProfile')->setName('user.edit.info');
	$this->post('/user/edit/info', 'AboutController:postEditProfile');

	$this->get('/user/edit/interests', 'InterestsController:getInterestsProfile')->setName('user.edit.interests');
	$this->post('/user/edit/interests', 'InterestsController:postInterestsProfile');
	$this->post('/user/edit/interests_delete', 'InterestsController:postDeleteInterestsProfile')->setName('user.edit.interests_delete');
	$this->post('/user/edit/interests_add', 'InterestsController:postAddInterestsProfile')->setName('user.edit.interests_add');
	
	$this->get('/user/edit/photo', 'PhotoController:getPhotoProfile')->setName('user.edit.photo');
	$this->post('/user/edit/photo_delete', 'PhotoController:postDeletePhotoProfile')->setName('user.edit.photo_delete');
	$this->post('/user/edit/photo', 'PhotoController:postPhotoProfile')->setName('user.edit.photo_post');

	$this->get('/search/all', 'SearchController:getAllProfile')->setName('search.all');
	$this->post('/search/like', 'LikedController:getLike')->setName('search.like');
	$this->post('/search/unlike', 'LikedController:getUnlike')->setName('search.unlike');
})->add(new AuthMiddleware($container));
