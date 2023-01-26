<?php

namespace App\Controller;

use RedBeanPHP\R;

class UserController extends Controller
{
    public function index()
    {
        self::render('user/add-user.html.twig');
    }

    public function listUsers()
    {
        $users = R::findAll('user');
        self::render('user/list-users.html.twig', [
            'users' => $users
        ]);
    }

    public static function addUser()
    {
        if (self::isFormSubmitted()) {
            $user = R::dispense('user');
            $user->username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $user->email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            R::store($user);
            header('location: /index.php?c=home');
        }
    }
}
