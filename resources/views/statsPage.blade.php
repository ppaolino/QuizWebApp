@extends('layouts.master')

@section('active_home', 'active')

@section('body')
    <div class="container-fluid">
        <div class="row justify-content-center g-0 text-center mb-3 mt-5">
            <div class="col-md-12">
                <h1>@lang('messages.myStatistics')</h1>
            </div>
        </div>

        @if ($totalQuizAttempted == 0)
            <div class="row justify-content-center mt-5">
                <div class="col-md-7 text-center">
                    <div class="alert alert-warning">
                        @lang('messages.noQuizAttempted')
                    </div>
                </div>
            </div>
        @else
            <div class="row justify-content-center g-4 text-center  mb-3 mt-4 ">
                <div class="col-lg-7">
                    <div class="card bg-info text-dark mb-3">
                        <div class="card-header">@lang('messages.generalStats')</div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.totalQuizzesAttempted')</div>
                                <div class="col-md-6 text-end">{{ $totalQuizAttempted }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.completionRate')</div>
                                <div class="col-md-6 text-end">{{ $completionRate['quiz_completati'] ?? 0 }}
                                    ({{ $completionRate['rate'] ?? 0 }}%)</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.winRate')</div>
                                <div class="col-md-6 text-end">{{ $winRatePercentage['quiz_vinti'] ?? 0 }}
                                    ({{ $winRatePercentage['rate'] ?? 0 }}%)</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.loseRate')</div>
                                <div class="col-md-6 text-end">
                                    {{ isset($completionRate['quiz_completati'], $winRatePercentage['quiz_vinti'])
                                        ? ($completionRate['quiz_completati'] - $winRatePercentage['quiz_vinti']) : '0' }}
                                    ({{ isset($completionRate['quiz_completati'], $winRatePercentage['quiz_vinti'])
                                        ? round(
                                            (($completionRate['quiz_completati'] - $winRatePercentage['quiz_vinti']) / $completionRate['quiz_completati']) *
                                                100,
                                            2,
                                        )
                                        : '0' }}%)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-info text-dark mb-3">
                        <div class="card-header">@lang('messages.accuracyStats')</div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.bestAccuracy')</div>
                                <div class="col-md-6 text-end">
                                    {{ $bestAccuracy['best_accuracy'] ?? '-' }}%
                                    <span class="badge bg-light text-dark">{{ $bestAccuracy['quiz_name'] ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.worstAccuracy')</div>
                                <div class="col-md-6 text-end">
                                    {{ $worstAccuracy['worst_accuracy'] ?? '-' }}%
                                    <span class="badge bg-light text-dark">{{ $worstAccuracy['quiz_name'] ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.bestAccuracyWin')</div>
                                <div class="col-md-6 text-end">
                                    {{ $bestAccuracyInWin['best_accuracy'] ?? '-' }}%
                                    <span class="badge bg-light text-dark">{{ $bestAccuracyInWin['quiz_name'] ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.bestAccuracyLost')</div>
                                <div class="col-md-6 text-end">
                                    {{ $bestAccuracyInLost['best_accuracy'] ?? '-' }}%
                                    <span class="badge bg-light text-dark">{{ $bestAccuracyInLost['quiz_name'] ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.worstAccuracyWin')</div>
                                <div class="col-md-6 text-end">
                                    {{ $worstAccuracyInWin['worst_accuracy'] ?? '-' }}%
                                    <span class="badge bg-light text-dark">{{ $worstAccuracyInWin['quiz_name'] ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.worstAccuracyLost')</div>
                                <div class="col-md-6 text-end">
                                    {{ $worstAccuracyInLost['worst_accuracy'] ?? '-' }}%
                                    <span
                                        class="badge bg-light text-dark">{{ $worstAccuracyInLost['quiz_name'] ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-info text-dark mb-3">
                        <div class="card-header">@lang('messages.streakStats')</div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.currentStreak')</div>
                                <div class="col-md-6 text-end">{{ $streakMetrics['current_streak'] ?? 0 }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 text-start">@lang('messages.longestStreak')</div>
                                <div class="col-md-6 text-end">{{ $streakMetrics['longest_streak'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-info text-dark mb-3">
                        <div class="card-header">@lang('messages.quizRanking')</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="quizRankingDropdown" class="form-label">@lang('messages.selectQuiz')</label>
                                <select id="quizRankingDropdown" class="form-select">
                                    <option value="" selected disabled>@lang('messages.chooseQuiz')</option>
                                    @foreach ($allQuizzes as $quiz)
                                        <option value="{{ $quiz->id }}" data-name="{{ $quiz->name }}">
                                            {{ $quiz->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="quizRankingStats" class="mt-3" style="display:none;">
                                <div class="row mb-2">
                                    <div class="col-md-6 text-start">@lang('messages.userAvgScore')</div>
                                    <div class="col-md-6 text-end" id="userAvgScore">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6 text-start">@lang('messages.globalAvgScore')</div>
                                    <div class="col-md-6 text-end" id="globalAvgScore">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    @endsection

    @push('scripts')
        <script>
            $('#quizRankingDropdown').on('change', function() {
                const quizId = $(this).val();
                if (!quizId) return;

                fetch(`/game-statistics/${quizId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.stats) {
                            $('#quizRankingStats').show();
                            $('#quizAnswersCount').text(data.stats.quiz_answers ?? '-');
                            $('#userAvgScore').text(
                                (data.stats.user_avg_score ?? '-') + ' / ' + (data.stats.quiz_answers ?? '-')
                            );
                            $('#globalAvgScore').text(
                                (data.stats.global_avg_score ?? '-') + ' / ' + (data.stats.quiz_answers ?? '-')
                            );
                        } else {
                            $('#quizRankingStats').hide();
                        }
                    })
                    .catch(() => {
                        $('#quizRankingStats').hide();
                    });
            });
        </script>
    @endpush
