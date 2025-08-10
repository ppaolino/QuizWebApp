<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LangController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizAnswerController;
use App\Http\Controllers\UserAnswerController;

/*
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
*/

require __DIR__ . '/auth.php';

Route::get('/lang/{lang}', [LangController::class, 'changeLanguage'])->name('setLang');

Route::middleware(['lang'])->group(function () {
    Route::get('/', [FrontController::class, 'getHome'])->name('home');

    Route::middleware(['auth', 'isUser'])->group(function () {




        /*
        Route::get('/quiz/search', [QuizController::class, 'search'])->name('quiz.search');  // search for specific quizzes, useful with ajax/a lot of quizzes

        Route::post('/quiz_attempt', [QuizAttemptController::class, 'store'])->name('quizAttempt.store'); // create a new quiz attempt, gets the attempt id

        Route::get('/quiz/{quiz}/answers', [QuizAnswerController::class, 'indexByQuiz'])->name('quiz.answers.index'); // list all answers for the specific quiz



        Route::post('/user_answer', [UserAnswerController::class, 'store'])->name('userAnswer.store'); //submit a user answer, gets the result (correct or not)

        Route::delete('/quiz_attempt/{id}', [QuizAttemptController::class, 'delete'])->name('quizAttempt.delete'); // delete a quiz attempt
        */
    });


    Route::middleware(['auth', 'isCreator'])->group(function () {

        /*
        Route::resource('quiz', QuizController::class); // create, edit, delete quizzes ???non serve edit???
        */
        Route::resource('quiz_answer', QuizAnswerController::class); // create, edit, delete quiz answers

        Route::delete('/quiz', [QuizController::class, 'destroy'])->name('quiz.destroy');

        Route::put('/quiz', [QuizController::class, 'update'])->name('quiz.update');

        Route::post('/quiz-available', [QuizController::class, 'available'])->name('quiz.available'); // verify if a quiz name is available

        Route::get('/create-quiz', [QuizController::class, 'quizCreationPage'])->name('create.quiz'); // verify that there are no drafts for the quiz creation page

    });


    Route::middleware(['auth', 'isAdmin'])->group(function () {
        /*
        Route::resource('league', LeagueController::class); // create, edit, delete leagues

        Route::resource('team', TeamController::class); // create, edit, delete teams

        Route::resource('player', PlayerController::class); // create, edit, delete players

        Route::get('/quiz/', [QuizController::class, 'index'])->name('quiz.index'); // list all quizzes

        Route::get('quiz/{id}', [QuizController::class, 'show'])->name('quiz.show'); // show a specific quiz ???serve???

        Route::put('/quiz/{id}', [QuizController::class, 'update'])->name('quiz.update'); // update a specific quiz, update=approved+published

        Route::delete('/quiz/{id}', [QuizController::class, 'destroy'])->name('quiz.destroy'); // delete a specific quiz
        */
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/players/search', [PlayerController::class, 'search'])->name('players.search'); // search for players

        Route::get('/teams/search', [TeamController::class, 'search'])->name('teams.search'); // search for teams

        Route::get('/leagues/search', [LeagueController::class, 'search'])->name('leagues.search'); // search for leagues

        Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index'); // list all quizzes

        //Route::post('/quizAttempt', [QuizAttemptController::class, 'store'])->name('quizAttempt.store'); // create a new attempt

        Route::post('/game/{id}/start', [QuizAttemptController::class, 'create'])->name('create.game'); // create a new attempt

        Route::get('/game/{id}/play', [QuizAttemptController::class, 'show'])->name('continue.game'); // create a new attempt

        Route::post('/userAnswer',[UserAnswerController::class, 'store'])->name('userAnswer.store'); // create a new user answer

    });
});
