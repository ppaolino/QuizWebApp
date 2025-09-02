<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class DataLayer
{
    public function search($name)
    {

        if (!$name) {
            return [];
        }

        $players = Player::where('name', 'LIKE', '%' . $name . '%')->limit(15)
            ->get(['id', 'name', 'position']);

        return $players;
    }
    public function searchTeams($name)
    {

        if (!$name) {
            return [];
        }

        $players = Team::where('name', 'LIKE', '%' . $name . '%')->limit(15)
            ->get(['id', 'name']);

        return $players;
    }
    public function searchLeagues($name)
    {

        if (!$name) {
            return [];
        }

        $players = League::where('name', 'LIKE', '%' . $name . '%')->limit(15)
            ->get(['id', 'name']);

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
        }])->findOrFail($quizId, ['id', 'name', 'prompt_text', 'language_id', 'max_errors', 'status', 'is_published']);

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
        $filtered = $quizzes->where('is_published', 1)->values();

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
    public function getAllQuizzes()
    {
        return Quiz::where('is_published',1)->get(['id', 'name']);
    }

    public function getNotApprovedQuizzes()
    {
        return Quiz::where('is_published', 0)
            ->where('status', 1)
            ->join('users', 'quiz.created_by', '=', 'users.id')
            ->get(['quiz.id', 'quiz.name', 'quiz.prompt_text', 'quiz.max_errors', 'users.name as creator_name']);
    }

    public function publishQuiz($userId, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $quiz->is_published = 1;
        $quiz->approved_by = $userId;
        $quiz->published_at = now();
        return $quiz->save();
    }

    public function destroyQuiz($userId, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $quiz->is_published = -1;
        $quiz->approved_by = $userId;
        $quiz->published_at = now();
        return $quiz->save();
    }


    //-----------------------------------STATISTICS-------------------------------------------

    // corretta
    public function getTotalQuizzesAttempted($user_id)
    {
        return QuizAttempt::where('user_id', $user_id)->count();
    }

    //corretta
    public function getCompletionRate($user_id)
    {
        $total = QuizAttempt::where('user_id', $user_id)->count();
        $completed = QuizAttempt::where('user_id', $user_id)
            ->whereIn('score', [1, 2])
            ->count();

        $compRate = round($total > 0 ? ($completed / $total) * 100 : 0, 2);

        return [
            'quiz_completati' => $completed,
            'rate' => $compRate
        ];
    }

    //corretta
    public function getWinRatePercentage($user_id)
    {
        $completed = QuizAttempt::where('user_id', $user_id)
            ->whereIn('score', [1, 2])
            ->count();
        $won = QuizAttempt::where('user_id', $user_id)
            ->where('score', 1)
            ->count();

        $rate = round($completed > 0 ? ($won / $completed) * 100 : 0, 2);

        return [
            'quiz_vinti' => $won,
            'rate' => $rate
        ];
    }

    //giusto
    public function getBestAccuracy($user_id)
    {
        // Only finished attempts (score 1 or 2)
        $attempts = QuizAttempt::where('user_id', $user_id)
            ->whereIn('score', [1, 2])
            ->get();

        if ($attempts->isEmpty()) {
            return null;
        }

        $bestAccuracy = 0;
        $bestQuizzes = [];

        foreach ($attempts as $attempt) {
            $totalGuess = UserAnswer::where('attempt_id', $attempt->id)->count();
            if ($totalGuess === 0) {
                continue;
            }

            $correctAnswers = UserAnswer::where('attempt_id', $attempt->id)
                ->where('is_correct', 1)
                ->count();

            $accuracy = ($correctAnswers / $totalGuess) * 100;

            if ($accuracy > $bestAccuracy) {
                $bestAccuracy = $accuracy;
                $bestQuizzes = [];
            }

            if ($accuracy == $bestAccuracy) {
                $quiz = Quiz::find($attempt->quiz_id);
                if ($quiz) {
                    $bestQuizzes[] = $quiz->name;
                }
            }
        }

        return [
            'best_accuracy' => round($bestAccuracy, 2),
            'quiz_name' => count($bestQuizzes) > 1 ? "multiple quizzes" : $bestQuizzes[0]
        ];
    }

    //giusto
    public function getWorstAccuracy($user_id)
    {
        // Only finished attempts (score 1 or 2)
        $attempts = QuizAttempt::where('user_id', $user_id)
            ->whereIn('score', [1, 2])
            ->get();

        if ($attempts->isEmpty()) {
            return null;
        }

        $worstAccuracy = 100;
        $worstQuizzes = [];

        foreach ($attempts as $attempt) {
            $totalGuess = UserAnswer::where('attempt_id', $attempt->id)->count();
            if ($totalGuess === 0) {
                continue;
            }

            $correctAnswers = UserAnswer::where('attempt_id', $attempt->id)
                ->where('is_correct', 1)
                ->count();

            $accuracy = ($correctAnswers / $totalGuess) * 100;

            if ($accuracy < $worstAccuracy) {
                $worstAccuracy = $accuracy;
                $worstQuizzes = [];
            }

            if ($accuracy == $worstAccuracy) {
                $quiz = Quiz::find($attempt->quiz_id);
                if ($quiz) {
                    $worstQuizzes[] = $quiz->name;
                }
            }
        }

        return [
            'worst_accuracy' => round($worstAccuracy, 2),
            'quiz_name' => count($worstQuizzes) > 1 ? "multiple quizzes" : $worstQuizzes[0]
        ];
    }

    //giusto
    public function getBestAccuracyInWin($user_id)
    {
        return $this->getAccuracyFiltered($user_id, 'best', 1);
    }

    //giusto
    public function getBestAccuracyInLost($user_id)
    {
        return $this->getAccuracyFiltered($user_id, 'best', 2);
    }

    //giusto
    public function getWorstAccuracyInWin($user_id)
    {
        return $this->getAccuracyFiltered($user_id, 'worst', 1);
    }

    //giusto
    public function getWorstAccuracyInLost($user_id)
    {
        return $this->getAccuracyFiltered($user_id, 'worst', 2);
    }

    // supporto
    private function getAccuracyFiltered($user_id, $type = 'best', $score = null)
    {
        $query = QuizAttempt::where('user_id', $user_id);

        if ($score) {
            $query->where('score', $score); // 1 = win, 2 = lost
        } else {
            $query->whereIn('score', [1, 2]); // finished attempts
        }

        $attempts = $query->get();
        if ($attempts->isEmpty()) {
            return null;
        }

        $extremeAccuracy = $type === 'best' ? 0 : 100;
        $quizzes = [];

        foreach ($attempts as $attempt) {
            $totalGuess = UserAnswer::where('attempt_id', $attempt->id)->count();
            if ($totalGuess === 0) {
                continue;
            }

            $correctAnswers = UserAnswer::where('attempt_id', $attempt->id)
                ->where('is_correct', 1)
                ->count();

            $accuracy = ($correctAnswers / $totalGuess) * 100;

            if (($type === 'best' && $accuracy > $extremeAccuracy) ||
                ($type === 'worst' && $accuracy < $extremeAccuracy)
            ) {
                $extremeAccuracy = $accuracy;
                $quizzes = [];
            }

            if ($accuracy == $extremeAccuracy) {
                $quiz = Quiz::find($attempt->quiz_id);
                if ($quiz) {
                    $quizzes[] = $quiz->name;
                }
            }
        }

        return [
            ($type === 'best' ? 'best_accuracy' : 'worst_accuracy') => round($extremeAccuracy, 2),
            'quiz_name' => count($quizzes) > 1 ? "multiple quizzes" : $quizzes[0]
        ];
    }

    //corretta
    public function getStreakMetrics($user_id)
    {
        $attempts = QuizAttempt::where('user_id', $user_id)
            ->whereIn('score', [1, 2]) // only finished attempts
            ->orderBy('started_at', 'desc')
            ->get();

        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 0;
        $firstAttempt = true;

        foreach ($attempts as $attempt) {
            if ($attempt->score === 1) {
                // Win
                $tempStreak++;
                if ($firstAttempt) {
                    $currentStreak++; // current streak counts from the latest attempts
                }
            } else if ($attempt->score === 2) {
                // Loss → reset streak
                $longestStreak = max($longestStreak, $tempStreak);
                $tempStreak = 0;
                $firstAttempt = false; // after the first loss, we stop counting current streak
            }
        }

        // Update longest streak if the last streak was the longest
        $longestStreak = max($longestStreak, $tempStreak);

        return [
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak
        ];
    }





    //corretta
    public function getQuizzesCreatedCount($creator_id)
    {
        return Quiz::where('created_by', $creator_id)->count();
    }

    //corretta
    public function getAverageAttemptsPerQuiz($creator_id)
    {
        $quizzes = Quiz::where('created_by', $creator_id)
            ->withCount('attempts')
            ->get();

        return round($quizzes->avg('attempts_count'), 2);
    }

    //corretta
    public function getGlobalWinRateForCreatedQuizzes($creator_id)
    {
        $attempts = QuizAttempt::whereHas('quiz', function ($query) use ($creator_id) {
            $query->where('created_by', $creator_id);
        })
            ->whereIn('score', [1, 2])
            ->count();

        $wins = QuizAttempt::whereHas('quiz', function ($query) use ($creator_id) {
            $query->where('created_by', $creator_id);
        })
            ->where('score', 1)
            ->count();

        return round($attempts > 0 ? ($wins / $attempts) * 100 : 0, 2);
    }

    //corretta
    public function getMostChallengingQuiz($creator_id)
    {
        return Quiz::where('created_by', $creator_id)
            ->withCount(['attempts as won_attempts' => function ($query) {
                $query->where('score', 1);
            }])
            ->withCount(['attempts as total_attempts' => function ($query) {
                $query->whereIn('score', [1, 2]);
            }])
            ->get()
            ->map(function ($quiz) {
                $quiz->difficulty_rate = $quiz->total_attempts > 0 ? ($quiz->won_attempts / $quiz->total_attempts) * 100 : 0;
                return $quiz;
            })
            ->sortBy('difficulty_rate')
            ->first()->only(['name', 'difficulty_rate']);
    }

    //corretta
    public function getPlayerFrequencyUsage($creator_id)
    {
        $totalUsages = QuizAnswer::whereHas('quiz', function ($query) use ($creator_id) {
            $query->where('created_by', $creator_id);
        })
            ->count();

        return QuizAnswer::whereHas('quiz', function ($query) use ($creator_id) {
            $query->where('created_by', $creator_id);
        })
            ->join('players', 'quiz_answers.player_id', '=', 'players.id')
            ->select('players.name as player_name')
            ->selectRaw('COUNT(*) as usage_count')
            ->selectRaw('ROUND((COUNT(*) / ?) * 100, 2) as usage_rate', [$totalUsages])
            ->groupBy('players.id', 'players.name')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();
    }

    //corretta
    public function getClueDistribution($creator_id)
    {
        $total = QuizAnswer::whereHas('quiz', function ($query) use ($creator_id) {
            $query->where('created_by', $creator_id);
        })
            ->count();

        $teamClues = QuizAnswer::whereHas('quiz', function ($query) use ($creator_id) {
            $query->where('created_by', $creator_id);
        })
            ->whereNotNull('team_id')
            ->count();

        $leagueClues = QuizAnswer::whereHas('quiz', function ($query) use ($creator_id) {
            $query->where('created_by', $creator_id);
        })
            ->whereNotNull('league_id')
            ->count();

        return [
            'team_clues_percentage' => $total > 0 ? round(($teamClues / $total) * 100, 2) : 0,
            'league_clues_percentage' => $total > 0 ? round(($leagueClues / $total) * 100, 2) : 0
        ];
    }

    //corretta
    public function getQuizDifficultyRank($user_id, $quiz_id)
    {
        // Total number of quiz answers
        $quizAnswersCount = QuizAnswer::where('quiz_id', $quiz_id)->count();

        // User stats: average correct answers per finished attempt
        $userAttempts = QuizAttempt::where('user_id', $user_id)
            ->where('quiz_id', $quiz_id)
            ->whereIn('score', [1, 2])
            ->pluck('id');

        $userAvgScore = 0;
        if ($userAttempts->isNotEmpty()) {
            $userAvgScore = UserAnswer::whereIn('attempt_id', $userAttempts)
                ->selectRaw('attempt_id, SUM(is_correct) as correct_per_attempt')
                ->groupBy('attempt_id')
                ->get()
                ->avg('correct_per_attempt');
        }

        // Global stats: average correct answers per finished attempt
        $globalAttempts = QuizAttempt::where('quiz_id', $quiz_id)
            ->whereIn('score', [1, 2])
            ->pluck('id');

        $globalAvgScore = 0;
        if ($globalAttempts->isNotEmpty()) {
            $globalAvgScore = UserAnswer::whereIn('attempt_id', $globalAttempts)
                ->selectRaw('attempt_id, SUM(is_correct) as correct_per_attempt')
                ->groupBy('attempt_id')
                ->get()
                ->avg('correct_per_attempt');
        }

        return [
            'quiz_answers' => $quizAnswersCount,
            'user_avg_score' => round($userAvgScore, 2),
            'global_avg_score' => round($globalAvgScore, 2)
        ];
    }


    //--------------------------------------------------------------------------------------------
    //DATABASE


    public function getPlayerById($id)
    {
        return Player::find($id);
    }

    public function addPlayer($name, $position)
    {
        $player = new Player();
        $player->name = $name;
        $player->position = $position;
        $player->save();

        return $player;
    }

    public function deletePlayer($id)
    {
        $player = Player::find($id);
        if ($player) {
            $player->delete();
            return true;
        }
        return false;
    }

    public function updatePlayer($id, $name = null, $position = null)
    {
        $player = Player::find($id);
        if ($player) {
            if ($name)
                $player->name = $name;
            if ($position)
                $player->position = $position;
            $player->save();
            return true;
        }
        return false;
    }

    //--------------------

    public function getTeamById($id)
    {
        return Team::find($id);
    }

    public function addTeam($name, $leagueId = 51, $logo = null)
    {
        $team = new Team();
        $team->name = $name;
        $team->league_id = $leagueId;
        $team->save();

        // Save logo if provided and is a PNG
        if ($logo && $logo->isValid() && $logo->getClientOriginalExtension() === 'png') {
            $logo->move(public_path('img/teams'), $team->id . '.png');
        }

        return $team;
    }

    public function updateTeam($teamId, $name = null, $logo = null)
    {
        $team = Team::find($teamId);
        if (!$team) {
            return false; // no league with that id
        }

        // Update name if provided
        if (!is_null($name)) {
            $team->name = $name;
            $team->save();
        }

        // Update logo if provided
        if ($logo) {
            $dest = public_path('img/teams');

            if (!File::exists($dest)) {
                File::makeDirectory($dest, 0755, true);
            }

            $logo->move($dest, $team->id . '.png'); // overwrites old file
        }

        return true;
    }

    public function deleteTeam($id)
    {
        $team = Team::find($id);
        if ($team) {
            $team->delete();
            return true;
        }
        return false;
    }

    //--------------------

    public function getLeagueById($id)
    {
        return League::find($id);
    }

    public function getAllLeagues()
    {
        return League::all();
    }

    public function addLeague(string $name, ?UploadedFile $logo = null): ?League
    {
        $league = new League();
        $league->name = $name;
        $league->save(); // we need the ID for naming

        if ($logo) {
            $dest = public_path('img/leagues');

            if (!File::exists($dest)) {
                File::makeDirectory($dest, 0755, true);
            }

            // Overwrite (or create) {id}.png
            $logo->move($dest, $league->id . '.png');
        }

        return $league;
    }

    public function updateLeague(int $id, ?string $name = null, ?UploadedFile $logo = null): bool
    {
        $league = League::find($id);
        if (!$league) {
            return false; // no league with that id
        }

        // Update name if provided
        if (!is_null($name)) {
            $league->name = $name;
            $league->save();
        }

        // Update logo if provided
        if ($logo) {
            $dest = public_path('img/leagues');

            if (!File::exists($dest)) {
                File::makeDirectory($dest, 0755, true);
            }

            $logo->move($dest, $league->id . '.png'); // overwrites old file
        }

        return true;
    }

    public function deleteLeague($id)
    {
        $league = League::find($id);
        if ($league) {
            $league->delete();
            return true;
        }
        return false;
    }
}
