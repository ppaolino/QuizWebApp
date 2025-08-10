<?php

namespace App\Http\Controllers;

class FrontController extends Controller
{
    public function getHome()
    {

        return view('index', [
            'titolo' => 'Quiz Web App',
            'descrizione' => 'Benvenuto nella Quiz Web App, dove puoi mettere alla prova le tue conoscenze!',
        ]);

/*
        return view('quizPage', [
            'titolo' => 'TITOLO',
            'descrizione' => 'descrizione del quiz',
        ]);
*/
    }

}
