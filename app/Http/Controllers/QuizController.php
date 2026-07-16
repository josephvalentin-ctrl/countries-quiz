<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function postAnswer()
    {
        return view('result');
    }
}

