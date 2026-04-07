<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataLayer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        $answer = $dl->addQuizAnswer($quizId,  $data);

        if (!$answer) {
            return response()->json(['success' => false, 'message' => 'Unable to save answer.'], 400);
        }

        $html = view('components.card-player', [
            'active' => false,
            'revealed' => false,
            'player' => $dl->getPlayerNameById($data['playerId']),
            'context' => $dl->getPlayerNameById($data['playerId'])." (".$data['context'].")",
            'image' => $image,
            'answerId' => $answer->answer_id,
            'deletable' => true,
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
        $quizId = session('active_quiz_id');

        if (!$quizId) {
            return response()->json(['success' => false, 'message' => 'No active draft quiz found.'], 400);
        }

        $dl = new DataLayer();
        $deleted = $dl->deleteQuizAnswer((int) $quizId, (int) $id, (int) Auth::id());

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Answer not found or not deletable.'], 404);
        }

        return response()->json(['success' => true]);
    }
}
