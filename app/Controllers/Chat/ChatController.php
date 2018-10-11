<?php

namespace Matcha\Controllers\Chat;
use Matcha\Controllers\Controller;

/**
 * Created by PhpStorm.
 * User: ozharko
 * Date: 10/11/18
 * Time: 6:53 PM
 */

class ChatController extends Controller
{
    public function index($request, $response)
    {
        return $this->view->render($response, 'chat/chat.twig');
    }
}