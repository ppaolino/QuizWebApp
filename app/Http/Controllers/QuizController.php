<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\DataLayer;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dl = new DataLayer();
        $userId = Auth::id();
        $quizzes = $dl->getQuizzes($userId);

        // Process each quiz to modify the prompt_text and replace language_id with language_code
        $processedQuizzes = $quizzes->map(function ($quiz) use ($dl) {
            // Find the position of the first asterisk
            $asteriskPos = strpos($quiz->prompt_text, '*');

            // If asterisk is found, truncate the string at that position
            if ($asteriskPos !== false) {
                $quiz->prompt_text = substr($quiz->prompt_text, 0, $asteriskPos);
            }

            // Replace language_id with language_code using DataLayer
            $quiz->language_code = $dl->getLanguageCodeById($quiz->language_id);
            unset($quiz->language_id);

            return $quiz;
        });

        return view('quizIndexPage', ['quizzes' => $processedQuizzes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function approve(Request $request)
    {
        $dl = new DataLayer();
        $userId = Auth::id();
        $dl->publishQuiz($userId, $request->id);
        return response()->json([
            'success' => true,
            'redirect_url' => route('approve.quiz'),
        ]);
    }

    public function notApprove(Request $request)
    {
        $dl = new DataLayer();
        $userId = Auth::id();
        $dl->destroyQuiz($userId,$request->id);
        return response()->json([
            'success' => true,
            'redirect_url' => route('approve.quiz'),
        ]);
    }
    public function show()
    {
        $dl = new DataLayer();
        $quiz = $dl->getNotApprovedQuizzes();

        if (!$quiz) {
            return redirect()->route('home')->with('error', 'Quiz not found.');
        }

        return view('quizIndexPageAdmin', [
            'quiz' => $quiz
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
    public function update(Request $request)
    {
        $prompt = $request->input('context');
        $errors = $request->input('max_errors');

        $dl = new DataLayer();
        $dl->updateQuiz('prompt_text', $prompt);
        $dl->updateQuiz('max_errors', $errors);
        $dl->updateQuiz('status', 1);

        session()->forget('active_quiz');
        session()->forget('active_quiz_id');

        return response()->json([
            'success' => true,
            'redirect_url' => route('home'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $dl = new DataLayer();
        $dl->deleteQuiz(session('active_quiz_id'));

        session()->forget('active_quiz');
        session()->forget('active_quiz_id');


        return response()->json(['message' => 'Quiz deleted successfully']);
    }

    public function available(Request $request)
    {
        $title = $request->input('title');
        $lang = $request->input('language', 'en'); // Default to 'en' if not provided

        $dl = new DataLayer();
        // Prendi solo i primi 10 risultati direttamente dalla query
        $answer = $dl->verifyTitleAvailability($title, $lang);
        return response()->json([
            'available' => $answer,
            'quiz_id' => session('active_quiz_id', null) // Ritorna l'ID del quiz attivo se esiste
        ]);
    }

    public function quizCreationPage()
    {
        $dl = new DataLayer();
        $active_quiz_answers = $dl->hasDraft();

        $cards = [];
        $quiz = session('active_quiz');
        $availableLanguages = $dl->getLanguages();
        $languages = $availableLanguages
            ->map(function ($lang) {
                return [
                    'abbr' => $lang->code,
                    'name' => $lang->name,
                ];
            })->toArray();

        if ($active_quiz_answers) {

            foreach ($active_quiz_answers as $answer) {
                if ($answer['team_id']) {
                    $image = asset('img/teams/' . $answer['team_id'] . '.png');
                } else if ($answer['league_id']) {
                    $image = asset('img/leagues/' . $answer['league_id'] . '.png'); // also fixed $data['team'] → $data['league']
                } else {
                    $image = asset('img/blank.jpg');
                }
                $html = view('components.card-player', [
                    'active' => false,
                    'revealed' => false,
                    'player' => $dl->getPlayerNameById($answer['player_id']),
                    'context' => $dl->getPlayerNameById($answer['player_id']) . " (" . $answer['context_info'] . ")",
                    'image' => $image,
                ])->render();

                $cards[] = $html;
            }
            $quizId = session('active_quiz_id');
        }
        return view('quizFactoryPage', compact('quiz', 'cards', 'languages'));
    }

    public function getStats($id)
    {
        $dl = new DataLayer();
        $userId = Auth::id();

        $stats = $dl->getQuizDifficultyRank($userId, $id);

        return response()->json([
            'stats' => $stats
        ]);
    }
}
