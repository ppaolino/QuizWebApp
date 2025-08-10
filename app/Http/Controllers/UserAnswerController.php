<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\DataLayer;

class UserAnswerController extends Controller
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
        $attempt_id = $request->input('attempt_id');
        $player_id = $request->input('player_id');


        $dl = new DataLayer();

        $player_name = $dl->getPlayerNameById($player_id);

        $correctQuizAnswerId = $dl->createUserAnswer($attempt_id,$player_id);

        if($correctQuizAnswerId > 0){
            $gameOver = $dl->checkGameOverWin($attempt_id);
        }
        else{
            $gameOver = $dl->checkGameOverLose($attempt_id);
        }

        if($gameOver != 0){
            $dl->updateQuizAttemptStatus($attempt_id, $gameOver);
        }

        return response()->json([
            'success' => true,
            'correct_quiz_answer_id' => $correctQuizAnswerId,
            'player_name' => $player_name,
            'game_over' => $gameOver
        ]);

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
