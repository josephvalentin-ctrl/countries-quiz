<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $countries = Cache::remember(
            'country_response',
            3600,
            function ()
            {
                return Http::get('https://countriesnow.space/api/v0.1/countries/capital')
                ->json();
            }
        );
        $countryList = $countries['data'];
        $country = $countryList[array_rand($countryList)];
        $name = $country['name'];
        $capital = $country['capital'];

        $capitals = array_column($countryList, 'capital');
        do {
            $wrong1 = $capitals[array_rand($capitals)];
        } while ($wrong1 === $capital);

        do {
            $wrong2 = $capitals[array_rand($capitals)];
        } while ($wrong2 === $capital || $wrong2 === $wrong1);

        $options = [$capital, $wrong1, $wrong2];
        foreach ($options as &$option) {
            if (empty($option)) {
            $option = "No capital";
            }
        }
        unset($option);
        shuffle($options);
        session(['country' => $name, 'correct_capital' => $capital]);

        return view('index', ['name' => $name, 'options' => $options]);
    }

    public function postAnswer()
    {
        return view('result');
    }
}

