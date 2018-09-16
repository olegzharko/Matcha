<?php

namespace Matcha\Controllers\Profile;


use Matcha\Controllers\Controller;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Matcha\Models\User;
use Matcha\Models\Photo;

class PhotoController extends Controller
{
    public function getPhotoProfile(Request $request, Response $response)
    {
        $allPhoto = Photo::getUserPhoto();
        $this->container->view->getEnvironment()->addGlobal('allphoto', $allPhoto);
        return $this->view->render($response, 'user/edit/photo.twig');
    }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $src = $directory . DIRECTORY_SEPARATOR . $filename;
        Photo::setUserPhoto($src);
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
        $uploadedFile = $uploadedFiles['photo'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $response->write('uploaded ' . $filename . '<br/>');
        }
        return $response->withRedirect($this->router->pathFor('user.edit.photo'));
    }

    public function postDeletePhotoProfile($request, $response)
    {
//        $interestWithTokenLikeKey = $request->getParsedBody();
//        $interestWithTokenLikeIndex = array_keys($interestWithTokenLikeKey);
//        $find = $interestWithTokenLikeIndex['0'];
//
//        $allRowCurrentInterests = InterestList::where('interest', $find)->first();
////        r($allRowCurrentInterests->id);die();
//        UserInterest::where('interest_id', $allRowCurrentInterests->id)->delete();
//
//        return $response->withRedirect($this->router->pathFor('user.edit.interests'));
    }
}