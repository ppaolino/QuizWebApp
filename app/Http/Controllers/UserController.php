<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\DataLayer;

class UserController extends Controller
{

    private function getStatistics()
    {
        $dl = new DataLayer();
        $userId = Auth::id();

        $totalQuizAttempted = $dl->getTotalQuizzesAttempted($userId);

        if ($totalQuizAttempted == 0) {
            return ['totalQuizAttempted' => 0];
        }

        $completionRate = $dl->getCompletionRate($userId);
        $winRatePercentage = $dl->getWinRatePercentage($userId);
        $bestAccuracy = $dl->getBestAccuracy($userId);
        $worstAccuracy = $dl->getWorstAccuracy($userId);
        $bestAccuracyInWin = $dl->getBestAccuracyInWin($userId);
        $bestAccuracyInLost = $dl->getBestAccuracyInLost($userId);
        $worstAccuracyInWin = $dl->getWorstAccuracyInWin($userId);
        $worstAccuracyInLost = $dl->getWorstAccuracyInLost($userId);
        $streakMetrics = $dl->getStreakMetrics($userId);
        $allQuizzes = $dl->getAllQuizzes();

        $dataObj = [
            'totalQuizAttempted' => $totalQuizAttempted,
            'completionRate' => $completionRate,
            'winRatePercentage' => $winRatePercentage,
            'bestAccuracy' => $bestAccuracy,
            'worstAccuracy' => $worstAccuracy,
            'bestAccuracyInWin' => $bestAccuracyInWin,
            'bestAccuracyInLost' => $bestAccuracyInLost,
            'worstAccuracyInWin' => $worstAccuracyInWin,
            'worstAccuracyInLost' => $worstAccuracyInLost,
            'streakMetrics' => $streakMetrics,
            'allQuizzes' => $allQuizzes
        ];
        return $dataObj;

    }
    public function showStatistics()
    {
        $dataObj = $this->getStatistics();

        return view('statsPage', $dataObj);
    }

    public function showStatisticsCreator()
    {
        $dataObj = $this->getStatistics();

        $dl = new DataLayer();
        $userId = Auth::id();

        $mostChallengingQuiz = $dl->getMostChallengingQuiz($userId);
        $playerFrequencyUsage = $dl->getPlayerFrequencyUsage($userId);
        $clueDistribution = $dl->getClueDistribution($userId);
        $quizzesCreatedCount = $dl->getQuizzesCreatedCount($userId);
        $averageAttemptsPerQuiz = $dl->getAverageAttemptsPerQuiz($userId);
        $globalWinRateForCreatedQuizzes = $dl->getGlobalWinRateForCreatedQuizzes($userId);

        $dataObjCreator = [
            'mostChallengingQuiz' => $mostChallengingQuiz,
            'playerFrequencyUsage' => $playerFrequencyUsage,
            'clueDistribution' => $clueDistribution,
            'quizzesCreatedCount' => $quizzesCreatedCount,
            'averageAttemptsPerQuiz' => $averageAttemptsPerQuiz,
            'globalWinRateForCreatedQuizzes' => $globalWinRateForCreatedQuizzes
        ];

        return view('statsPageCreator', array_merge($dataObj, $dataObjCreator));
    }

}
