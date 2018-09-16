<?php

namespace  Matcha\Controllers\Search;

use Matcha\Controllers\Controller;
use Slim\Http\Request;
use Slim\Http\Response;
use Matcha\Models\User;
use Matcha\Models\About;

class SearchController extends Controller
{
    public function getAllProfile($request, $response)
    {
        $user = User::where('id', $_SESSION['user'])->first();
        $about = About::where('user_id', $user->id)->first();
        $prefer = $about->sexual_pref;

        $allPrefer = About::where('sexual_pref', $prefer)->get();

        // все интересы юзера
        foreach ($allPrefer as $row) {
            $arr[] = $row->user_id;
        }

        // все юзеры
        foreach ($arr as $user_id) {
            $searchUser[] = User::where('id', $user_id)->first();
        }

        return $this->view->render($response, 'search/all.twig');
    }
}