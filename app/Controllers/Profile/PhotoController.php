<?php

namespace Matcha\Controllers\Profile;

use Matcha\Controllers\Controller;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Matcha\Models\Photo;

class PhotoController extends Controller
{
	public function getPhotoProfile(Request $request, Response $response)
	{
		$allPhoto = Photo::getUserPhoto();
		if ($allPhoto)
			$this->container->view->getEnvironment()->addGlobal('allphoto', $allPhoto);
		return $this->view->render($response, 'user/edit/photo.twig');
	}

	public function moveUploadedFile($directory, UploadedFile $uploadedFile, $userdir)
	{
		$extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
		$basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
		$filename = sprintf('%s.%0.8s', $basename, $extension);
		$photo_src = '/img/' . $userdir . DIRECTORY_SEPARATOR . $filename;
		Photo::setUserPhoto($photo_src);
		$src = $directory . DIRECTORY_SEPARATOR . $filename;
		$uploadedFile->moveTo($src);
		return $filename;
	}

	public function postPhotoProfile(Request $request, Response $response)
	{
		$userdir = $_SESSION['user'];
		$directory = $this->upload_directory . "/" . $userdir;
		if ( !file_exists($directory))
			mkdir($directory);
		$uploadedFiles = $request->getUploadedFiles();
		// handle single input with single file upload
		if ($uploadedFiles)
		{
			$uploadedFile = $uploadedFiles['photo'];
			if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
				$filename = $this->moveUploadedFile($directory, $uploadedFile, $userdir);
				$response->write('uploaded ' . $filename . '<br/>');
			}
		}
		// handle multiple inputs with the same key
	    // foreach ($uploadedFiles['photo'] as $uploadedFile) {
	    //     if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
	    //         $filename = $this->moveUploadedFile($directory, $uploadedFile, $userdir);
	    //         $response->write('uploaded ' . $filename . '<br/>');
	    //     }
	    // }
		// return $response->withRedirect($this->router->pathFor('user.edit.photo'));
		// return $response->withRedirect($this->router->pathFor('auth.edit.user'));
	}
	
	public function postDeletePhotoProfile($request, $response)
	{
		$photoWithTokenLikeKey = $request->getParsedBody();
		$photoWithTokenLikeIndex = array_keys($photoWithTokenLikeKey);
		$src = $photoWithTokenLikeIndex['0'];
		$src = preg_replace('/_/', '.', $src);
		$src = str_replace('http://127.0.0.1:8800', '', $src);
		Photo::delUserPhoto($src);
		echo $src;
		// return $response->withRedirect($this->router->pathFor('auth.edit.user'));
	}
}

