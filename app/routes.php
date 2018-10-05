<?php
use Matcha\Middleware\AuthMiddleware;
use Matcha\Middleware\GuestMiddleware;
/*
 * так как мы добавили контроллер HomeController в container (app.php)
 * мы можем ипользовать его методы чере одинарное двоиточие
 */
$app->group('', function () {
	/*
	** sign up routes
	*/
	$this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
	$this->post('/auth/signup', 'AuthController:postSignUp');
	/*
	** sign in routes
	*/
	$this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
	$this->post('/auth/signin', 'AuthController:postSignIn');
	/*
	** reset password routes
	*/
	$this->get('/auth/password/forgot', 'AuthController:getResetPassword')->setName('auth.password.forgot');
	$this->post('/auth/password/forgot', 'AuthController:postResetPassword');
	/*
	** activate account routes
	*/
	$this->post('/activate', 'ActivateController:activate');
})->add(new GuestMiddleware($container));

$app->group('', function () {
	/*
	** user home page
	*/
	$this->get('/', 'HomeController:index')->setName('home');
	/*
	** edit profile
	*/
	$this->get('/auth/edit/user', 'EditController:getChangeProfile')->setName('auth.edit.user');
	$this->post('/auth/edit/user', 'EditController:postChangeProfile');
	/*
	** edit profile (handle user photo)
	*/
	$this->post('/user/edit/photo_delete', 'PhotoController:postDeletePhoto')->setName('user.edit.photo_delete');
	$this->post('/user/edit/photo_upload', 'PhotoController:postUploadPhoto')->setName('user.edit.photo_post');
	/*
	** sign out
	*/
	$this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');
	/*
	** account settings (change email and password)
	*/
	$this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/auth/password/change', 'PasswordController:postChangePassword');
	/*
	** account settings (reset password)
	*/
	$this->post('/auth/password/reset', 'PasswordController:postResetPassword');
	



	$this->get('/user/edit/info', 'AboutController:getEditProfile')->setName('user.edit.info');
	$this->post('/user/edit/info', 'AboutController:postEditProfile');

	$this->get('/user/edit/interests', 'InterestsController:getInterestsProfile')->setName('user.edit.interests');
	$this->post('/user/edit/interests', 'InterestsController:postInterestsProfile');
	$this->post('/user/edit/interests_delete', 'InterestsController:postDeleteInterestsProfile')->setName('user.edit.interests_delete');
	$this->post('/user/edit/interests_add', 'InterestsController:postAddInterestsProfile')->setName('user.edit.interests_add');
	
	// $this->get('/user/edit/photo', 'PhotoController:getPhotoProfile')->setName('user.edit.photo');
	

	$this->get('/search/all', 'SearchController:getAllProfile')->setName('search.all');
	$this->post('/search/like', 'LikedController:getLike')->setName('search.like');
	$this->post('/search/unlike', 'LikedController:getUnlike')->setName('search.unlike');
})->add(new AuthMiddleware($container));
