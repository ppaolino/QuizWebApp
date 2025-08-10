<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;

class DataLayer
{
    public function search($name)
    {

        if (!$name) {
            return [];
        }

        $players = Player::where('name', 'LIKE', '%' . $name . '%')->limit(15)
            ->get(['id', 'name', 'position']); // seleziona solo name e position

        return $players;
    }
    public function searchTeams($name)
    {

        if (!$name) {
            return [];
        }

        $players = Team::where('name', 'LIKE', '%' . $name . '%')->limit(15)
            ->get(['id', 'name']); // seleziona solo name e position

        return $players;
    }
    public function searchLeagues($name)
    {

        if (!$name) {
            return [];
        }

        $players = League::where('name', 'LIKE', '%' . $name . '%')->limit(15)
            ->get(['id', 'name']); // seleziona solo name e position

        return $players;
    }



    //creator
    public function verifyTitleAvailability($name, $lang)
    {

        if (!$name) {
            return false;
        }

        $exists = Quiz::where('name', $name)->exists();

        if (!$exists) {
            $langId = LanguageAvailable::where('code', $lang)->value('id');
            $quiz = Quiz::create([
                'name' => $name,           // the title
                'created_by' => auth()->id(),                // current user
                'status' => 0,
                'language_id' => $langId, // set the language ID
            ]);
            session(['active_quiz_id' => $quiz->id]);
        }

        return !$exists;
    }

    //creator
    public function createQuizAttempt($quiz_id)
    {
        if (!$quiz_id) {
            return false;
        }

        $quizAttempt = QuizAttempt::create([
            'quiz_id' => $quiz_id,           // the title
            'user_id' => auth()->id(),                // current user
            'score' => 0                                 // optional: mark as in progress
        ]);
        return $quizAttempt->id;
    }
    public function getQuizAttempt($quiz_id, $user_id)
    {
        $attempt = QuizAttempt::where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->where('score', 0) // Assuming score 0 means in progress
            ->first(['id']);

        return $attempt ? $attempt->id : null;
    }


    public function createUserAnswer($attempt_id, $player_id)
    {

        // 1. Fetch the quiz_id for the given attempt
        $attempt = QuizAttempt::findOrFail($attempt_id);
        $quizId = $attempt->quiz_id;

        // 2. Get all user answers for this attempt
        $userAnswers = UserAnswer::where('attempt_id', $attempt_id)
            ->where('player_id', $player_id)
            ->get();

        // 3. Get all correct quiz answers for this quiz with the given player_id
        $correctAnswers = QuizAnswer::where('quiz_id', $quizId)
            ->where('player_id', $player_id)
            ->orderBy('answer_id') // ensure deterministic order
            ->get();

        // 4. Determine if the answer is correct
        $usedCount = $userAnswers->count();
        $correctCount = $correctAnswers->count();

        $isCorrect = $usedCount < $correctCount;

        // 5. If correct, get the corresponding QuizAnswer id (based on how many were already used)
        $correctQuizAnswerId = -1;
        if ($isCorrect) {
            $correctQuizAnswer = $correctAnswers[$usedCount] ?? null;
            if ($correctQuizAnswer) {
                $correctQuizAnswerId = $correctQuizAnswer->answer_id;
            }
        }

        // 6. Store the UserAnswer
        UserAnswer::create([
            'attempt_id' => $attempt_id,
            'player_id'  => $player_id,
            'is_correct' => $isCorrect ? 1 : 0
        ]);

        // 7. Return the result
        return $correctQuizAnswerId;
    }
    public function getUserAnswers($attempt_id)
    {
        return UserAnswer::where('attempt_id', $attempt_id)
            ->get(['user_answer_id', 'player_id', 'is_correct']);
    }
    public function isAnswerAlreadyGuessed($attempt_id, QuizAnswer $quizAnswer)
    {
        // 1. Get the quiz_id and player_id from the QuizAnswer
        $quizId = $quizAnswer->quiz_id;
        $playerId = $quizAnswer->player_id;
        $answerId = $quizAnswer->answer_id;

        // 2. Get all correct answers for this quiz-player combination (ordered the same way)
        $correctAnswers = QuizAnswer::where('quiz_id', $quizId)
            ->where('player_id', $playerId)
            ->orderBy('answer_id')
            ->get();

        // 3. Get all user answers for this attempt-player combination
        $userAnswers = UserAnswer::where('attempt_id', $attempt_id)
            ->where('player_id', $playerId)
            ->orderBy('user_answer_id') // assuming answers are processed in creation order
            ->get();

        // 4. For each correct answer that was marked as correct in user answers,
        // check if it matches our target answer
        foreach ($userAnswers as $index => $userAnswer) {
            if ($userAnswer->is_correct && isset($correctAnswers[$index])) {
                if ($correctAnswers[$index]->answer_id == $answerId) {
                    return true; // answer was already guessed
                }
            }
        }

        return false; // answer wasn't found in previously guessed correct answers
    }

    public function getQuizAttemptErrors($quiz_attempt)
    {
        // Count the number of incorrect answers in the quiz attempt
        return UserAnswer::where('attempt_id', $quiz_attempt)
            ->where('is_correct', 0)
            ->select('player_id')
            ->get();
    }

    public function checkGameOverLose($attemptId)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        $maxErrors = Quiz::findOrFail($attempt->quiz_id)->max_errors;

        $incorrectCount = UserAnswer::where('attempt_id', $attemptId)
            ->where('is_correct', 0)
            ->count();


        if ($incorrectCount < $maxErrors) {
            return 0; // Not game over
        }


        return -1; // Game over
    }
    public function checkGameOverWin($attemptId)
    {
        // Check if the attempt has reached the maximum number of errors
        $attempt = QuizAttempt::findOrFail($attemptId);
        // Count the number of correct answers
        $correctCount = UserAnswer::where('attempt_id', $attemptId)
            ->where('is_correct', 1)
            ->count();

        $correctAnswers = QuizAnswer::where('quiz_id', $attempt->quiz_id)->count();

        if ($correctCount < $correctAnswers) {
            return 0; // Not game over
        }

        return 1; // Game over
    }


    //creator
    public function hasDraft()
    {
        $draftQuiz = Quiz::where('created_by', auth()->id())
            ->where('status', 0)
            ->first();

        if ($draftQuiz) {
            session(['active_quiz' => $draftQuiz]);
            session(['active_quiz_id' => $draftQuiz->id]);
            $active_quiz_answers = QuizAnswer::where('quiz_id', $draftQuiz->id)->get();
            return $active_quiz_answers;
        } else
            return null;
    }

    //creator
    public function addQuizAnswer($quizId, $answerData)
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return false;
        }

        $answer = new QuizAnswer();
        $answer->quiz_id = $quizId;
        $answer->player_id = $answerData['playerId'];
        $answer->context_info = $answerData['context'];
        if ($answerData['team']) {
            $answer->team_id = $answerData['team'];
        }
        if ($answerData['league']) {
            $answer->league_id = $answerData['league'];
        }
        $answer->save();

        return $answer;
    }

    function getPlayerNameById($id)
    {
        $player = Player::find($id);
        if (!$player) {
            return null;
        }

        return $player->name;
    }

    function getQuizAnswersById($quizId)
    {
        return QuizAnswer::where('quiz_id', $quizId)->get();
    }

    //creator
    public function deleteQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();
    }

    //creator
    function updateQuiz(string $field, $value): bool
    {
        // Find the record by its primary key (usually 'id')
        $quizId = session('active_quiz_id');
        $model = Quiz::find($quizId);

        if (!$model) {
            // You might want to handle the "not found" case differently
            return false;
        }

        // Update the specified field
        $model->$field = $value;

        // Save changes to the database
        return $model->save();
    }

    // Helper function to get a single quiz with status
    public function getQuiz($userId, $quizId)
    {
        $quiz = Quiz::with(['attempts' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->findOrFail($quizId, ['id', 'name', 'prompt_text', 'language_id', 'max_errors', 'status']);

        if ($quiz->attempts->isEmpty()) {
            $quiz->state = -1;
        } elseif ($quiz->attempts->contains('score', 0)) {
            $quiz->state = 0;
        } elseif ($quiz->attempts->contains('score', 1)) {
            $quiz->state = 1;
        } else {
            $quiz->state = 2;
        }

        unset($quiz->attempts);

        return $quiz;
    }

    public function getQuizzes($userId)
    {
        // Get all quiz IDs and basic info
        $quizIds = Quiz::pluck('id');

        // Map each ID to the processed single quiz data
        $quizzes = $quizIds->map(function ($quizId) use ($userId) {
            return $this->getQuiz($userId, $quizId);
        });

        // Filter quizzes where status == 1 (e.g. passed)
        $filtered = $quizzes->where('status', 1)->values();

        return $filtered;
    }

    public function updateQuizAttemptStatus($attemptId, $status)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        if ($status == 1) {
            $attempt->score = 1;
        } else {
            $attempt->score = 2;
        }
        return $attempt->save();
    }

    public function getLanguages()
    {
        return LanguageAvailable::all(['name', 'code']);
    }

    public function getQuizLanguageById($id)
    {
        $quiz = Quiz::with('language')->find($id);

        if ($quiz && $quiz->language) {
            return $quiz->language->code;
        }

        return $quiz->language->code ?? 'en';
    }
    public function getLanguageCodeById($languageId)
    {
        return LanguageAvailable::where('id', $languageId)->value('code');
    }
}
