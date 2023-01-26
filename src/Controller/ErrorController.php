<?php

namespace App\Controller;

class ErrorController extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function error404() {
        self::render('error/404.html.twig');
    }
}