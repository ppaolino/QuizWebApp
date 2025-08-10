<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\DataLayer;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class QuizAttemptController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function createAnswer($answer)
    {
        $dl = new DataLayer();
        if ($answer->team_id) {
            $image = asset('img/teams/' . $answer->team_id . '.png');
        } else if ($answer->league_id) {
            $image = asset('img/leagues/' . $answer->league_id . '.png');
        } else {
            $image = asset('img/blank.jpg');
        }

        $card = [
            'active' => false,
            'player' => $dl->getPlayerNameById($answer->player_id),
            'context' => $answer->context_info,
            'image' => $image,
            'answer_id' => $answer->answer_id
        ];

        return $card;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($quizId)
    {
        $dl = new DataLayer();

        $decoded = Hashids::decode($quizId);
        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];
        $userId = Auth::id();


        $quiz = $dl->getQuiz($userId, $id);
        $quiz_answers = $dl->getQuizAnswersById($id);
        $quiz_attempt = $dl->createQuizAttempt($id);

        $playerCards = []; // Renamed from $html

        foreach ($quiz_answers as $answer) {
            $playerCards[] = $this->createAnswer($answer);
        }

        return view('quizPage', [
            'quiz_attempt' => $quiz_attempt,
            'quiz' => $quiz,
            'playerCards' => $playerCards,
            'errors' => 0,
            'errors_players' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /*
    public function store(Request $request)
    {
        $quiz_id = $request->input('quiz_id');

        $userId = Auth::id();

        $dl = new DataLayer();
        $quiz = $dl->getQuiz($userId, $quiz_id);
        $quiz_answers = $dl->getQuizAnswersById($quiz_id);

        $playerCards = []; // Renamed from $html

        foreach ($quiz_answers as $answer) {
            if ($answer->team_id) {
                $image = asset('img/teams/' . $answer->team_id . '.png');
            } else if ($answer->league_id) {
                $image = asset('img/leagues/' . $answer->league_id . '.png');
            } else {
                $image = asset('img/blank.jpg');
            }

            $playerCards[] = [
                'active' => false,
                'player' => $dl->getPlayerNameById($answer->player_id),
                'context' => $answer->context_info,
                'image' => $image,
                'answer_id' => $answer->answer_id
            ];
        }

        $errors = 0;
        $errors_players = [];
        $errors_players_name = [];
        if ($quiz->state != 0) {
            $quiz_attempt = $dl->createQuizAttempt($quiz_id);
        } else {
            //come sopra ma invece di creare quiz_attempt, va preso e vanno aggiustate le risposte già date/errori già commessi
            $quiz_attempt = $dl->getQuizAttempt($quiz_id, $userId);
            $number_answers = $playerCards ? count($playerCards) : 0;

            for ($i = 0; $i < $number_answers; $i++) {
                if ($dl->isAnswerAlreadyGuessed($quiz_attempt, $quiz_answers[$i])) {
                    $playerCards[$i]['active'] = true;
                }
            }
            $errors_players = $dl->getQuizAttemptErrors($quiz_attempt);
            foreach ($errors_players as $error) {
                $errors_players_name[] = $dl->getPlayerNameById($error->player_id);
            }
            $errors = count($errors_players);
        }

        return view('quizPage', [
            'quiz_attempt' => $quiz_attempt,
            'quiz' => $quiz,
            'playerCards' => $playerCards,
            'errors' => $errors,
            'errors_players' => $errors_players_name
        ]);
    }
        */

    /**
     * Display the specified resource.
     */
    public function show(string $quizId)
    {
        $dl = new DataLayer();

        $decoded = Hashids::decode($quizId);
        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];
        $userId = Auth::id();


        $quiz = $dl->getQuiz($userId, $id);
        $quiz_answers = $dl->getQuizAnswersById($id);

        $errors = 0;
        $playerCards = []; // Renamed from $html
        $errors_players = [];
        $errors_players_name = [];

        foreach ($quiz_answers as $answer) {
            $playerCards[] = $this->createAnswer($answer);
        }
        $quiz_attempt = $dl->getQuizAttempt($id, $userId);
        if ($quiz_attempt == null) {
            abort(409); // Conflict, quiz attempt not found
        }
        $number_answers = $playerCards ? count($playerCards) : 0;

        for ($i = 0; $i < $number_answers; $i++) {
            if ($dl->isAnswerAlreadyGuessed($quiz_attempt, $quiz_answers[$i])) {
                $playerCards[$i]['active'] = true;
            }
        }
        $errors_players = $dl->getQuizAttemptErrors($quiz_attempt);
        foreach ($errors_players as $error) {
            $errors_players_name[] = $dl->getPlayerNameById($error->player_id);
        }
        $errors = count($errors_players);

        return view('quizPage', [
            'quiz_attempt' => $quiz_attempt,
            'quiz' => $quiz,
            'playerCards' => $playerCards,
            'errors' => $errors,
            'errors_players' => $errors_players_name
        ]);
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
