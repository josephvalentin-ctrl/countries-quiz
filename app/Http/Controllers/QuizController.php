<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Requests\AnswerValidationRequest;

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
        if (!isset($countries['data'])) {
            abort(500, 'Unable to load country data.');
        }

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

    public function postAnswer(AnswerValidationRequest $request)
    {
        if (empty($capital)) {
            $capital = "No capital";
        }
        $correctCapital = session('correct_capital');

        if ($correctCapital === null) {
            return redirect()->route('index')
            ->with('error', 'Your session expired. Please try a new question.');
        }

        $isCorrect = ($request->capital === $correctCapital);

        return view('result', ['correct' => $isCorrect, 'correctCapital' => $correctCapital, 'userAnswer' => $request->capital, 'country' => session('country')]);
    }
}

