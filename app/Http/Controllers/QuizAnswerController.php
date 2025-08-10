<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataLayer;
use Illuminate\Support\Facades\Log;

class QuizAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->json()->all();
        $dl = new DataLayer();
        $quizId = session('active_quiz_id');
        if ($data['team']) {
            $image = asset('img/teams/' . $data['team'] . '.png');
        } else if ($data['league']) {
            $image = asset('img/leagues/' . $data['league'] . '.png'); // also fixed $data['team'] → $data['league']
        } else {
            $image = asset('img/blank.jpg');
        }

        $dl->addQuizAnswer($quizId,  $data);

        $html = view('components.card-player', [
            'active' => false,
            'revealed' => false,
            'player' => $dl->getPlayerNameById($data['playerId']),
            'context' => $dl->getPlayerNameById($data['playerId'])." (".$data['context'].")",
            'image' => $image,
        ])->render();

        return response()->json(['success' => true, 'html' => $html]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
