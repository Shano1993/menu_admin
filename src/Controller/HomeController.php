<?php

namespace App\Controller;

class HomeController extends Controller
{
    public function index()
    {
        self::render('home/home.html.twig');
    }
}