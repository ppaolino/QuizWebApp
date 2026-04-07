@extends('layouts.master')

@section('active_home', 'active')

@section('body')

    <div class="container">
        <div class="row mt-4 mb-2">
            <div class="col-12 d-flex justify-content-start">
                <a href="{{ route('quiz.index') }}" class="btn btn-outline-primary">
                    Back to Quiz List
                </a>
            </div>
        </div>
        <div id="guess-feedback-banner" class="alert d-none quiz-feedback-banner" role="status"
            aria-live="polite" aria-atomic="true">
            <div class="fw-semibold" id="guess-feedback-banner-title"></div>
            <div id="guess-feedback-banner-body"></div>
        </div>
        <div class="row mb-3 mt-5">
            <div class="col-md-12 mx-auto d-flex justify-content-center align-items-center">
                <h1 id="quizTitolo">{{ $quiz->name }}</h1>
                <button type="button" class="btn btn-link text-primary ms-2 p-0 border-0" data-bs-toggle="tooltip"
                    data-bs-html="true" data-bs-placement="right"
                    title="@lang('messages.quizHelpTooltip')" aria-label="@lang('messages.quizHelpLabel')">
                    <i class="bi bi-question-circle-fill fs-4"></i>
                </button>
            </div>
        </div>
        <div class="row justify-content-center g-0 text-center mb-3" id="context_area">
        </div>

        <div id="guess-controls-placeholder"></div>
        <div id="guess-controls">
            <div id="guess-row" class="row mb-1">
                <x-bar-search type="players" />
            </div>
            <div class="row mb-1">
                <div class="col-sm-3 mx-auto d-flex justify-content-center align-items-center">
                    <button type="button" class="btn btn-primary" id="guess-button">
                        Guess
                    </button>
                </div>
            </div>
        </div>
        <div class="row mb-3 mt-3">
            <div class="col-sm-3 mx-auto d-flex justify-content-center align-items-center">
                <a id="reveal-button" onclick="revealCards()" class="btn btn-primary d-none disabled" tabindex="-1"
                    aria-disabled="true">
                    Reveal answers
                </a>
            </div>
        </div>
        <div class="row mb-3 mt-3">
            <div class="col-sm-3 mx-auto d-flex justify-content-center align-items-center">
                <a id="myButtonLink" href="{{ route('quiz.index') }}" class="btn btn-primary d-none disabled" tabindex="-1"
                    aria-disabled="true">
                    Back to Quiz List
                </a>
            </div>
        </div>


        <div class="row justify-content-center g-0 text-center mb-3">

            @for ($i = 0; $i < $quiz->max_errors; $i++)
                <div class="col-auto px-1 box-score">
                    <i class="bi bi-x-circle text-white h2" id="error{{ $i }}" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-placement="top" data-bs-content="" data-bs-html="true"></i>
                </div>
            @endfor
        </div>



        <div class="row no-gutters justify-content-center g-0 text-cente mb-3">
            @foreach ($playerCards as $card)
                <div class="col col-lg-2 col-md-3 col-sm-4 col-xs-4 d-flex justify-content-center align-items-center mb-3">
                    <x-card-player :active="$card['active']" :player="$card['player']" :context="$card['context']" :image="$card['image']"
                        :answerId="$card['answer_id']" />
                </div>
            @endforeach
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const quiz_attempt = @json($quiz_attempt);
        const quiz = @json($quiz);
        const $playersSearch = $('#players-search');
        const $guessButton = $('#guess-button');
        const guessControls = document.getElementById('guess-controls');
        const guessControlsPlaceholder = document.getElementById('guess-controls-placeholder');
        const feedbackBanner = document.getElementById('guess-feedback-banner');
        const feedbackBannerTitle = document.getElementById('guess-feedback-banner-title');
        const feedbackBannerBody = document.getElementById('guess-feedback-banner-body');

        let errorCount = @json($errors);
        const $errors = @json($errors_players);
        let feedbackBannerTimer = null;


        $(document).ready(function() {

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover focus',
                });
            });

            let guessControlsTop = 0;

            function updateGuessControlsTop() {
                if (!guessControls) {
                    return;
                }
                guessControlsTop = guessControls.getBoundingClientRect().top + window.scrollY;
            }

            function syncGuessControlsFixedState() {
                if (!guessControls || !guessControlsPlaceholder) {
                    return;
                }

                const shouldFix = window.scrollY > guessControlsTop;
                if (shouldFix) {
                    guessControls.classList.add('is-fixed');
                    guessControlsPlaceholder.style.display = 'block';
                    guessControlsPlaceholder.style.height = `${guessControls.offsetHeight}px`;
                } else {
                    guessControls.classList.remove('is-fixed');
                    guessControlsPlaceholder.style.display = 'none';
                    guessControlsPlaceholder.style.height = '0px';
                }
            }

            function syncGuessButtonState() {
                const selectedId = $playersSearch.data('data-selected-value');
                const hasSelection = selectedId !== undefined && selectedId !== null && selectedId !== '';
                $guessButton.prop('disabled', !hasSelection);
            }

            function isElementInViewport(element) {
                if (!element) {
                    return false;
                }

                const rect = element.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

                return rect.top >= 0 && rect.left >= 0 && rect.bottom <= viewportHeight && rect.right <= (window.innerWidth || document.documentElement.clientWidth);
            }

            function scrollElementIntoViewIfNeeded(element) {
                if (!element || isElementInViewport(element)) {
                    return;
                }

                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            function pulseElement(element) {
                if (!element) {
                    return;
                }

                element.classList.remove('quiz-feedback-pulse');
                void element.offsetWidth;
                element.classList.add('quiz-feedback-pulse');

                window.setTimeout(function() {
                    element.classList.remove('quiz-feedback-pulse');
                }, 700);
            }

            function showFeedbackBanner(type, title, body) {
                if (!feedbackBanner || !feedbackBannerTitle || !feedbackBannerBody) {
                    return;
                }

                if (feedbackBannerTimer) {
                    window.clearTimeout(feedbackBannerTimer);
                }

                feedbackBanner.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-info');
                feedbackBanner.classList.add(type === 'success' ? 'alert-success' : type === 'danger' ? 'alert-danger' : 'alert-info');
                feedbackBannerTitle.textContent = title;
                feedbackBannerBody.textContent = body;

                feedbackBannerTimer = window.setTimeout(function() {
                    feedbackBanner.classList.add('d-none');
                }, 4000);
            }

            // Start disabled until a valid typeahead selection is made.
            syncGuessButtonState();
            $playersSearch.on('typeahead:select typeahead:autocomplete input blur change keyup', syncGuessButtonState);
            updateGuessControlsTop();
            syncGuessControlsFixedState();
            window.addEventListener('scroll', syncGuessControlsFixedState);
            window.addEventListener('resize', function() {
                syncGuessControlsFixedState();
                updateGuessControlsTop();
                syncGuessControlsFixedState();
            });

            for (let j = 0; j < errorCount; j++) {
                markError(j, $errors[j], false);
            }



            document.body.classList.remove('bodyblue');
            document.body.classList.add('bodyyellow');

            const input = quiz.prompt_text;
            const lines = input.split('\n');
            const output = $('#context_area');

            output.empty(); // Clear previous content

            lines.forEach(function(line) {
                const $p = $('<div class="col-sm-12 align-self-center"></div>');
                if ($.trim(line).startsWith('*')) {
                    const content = $.trim(line).trim();
                    const $em = $('<em></em>').text(content);
                    $p.append($em);
                } else {
                    $p.text(line);
                }
                output.append($p);
            });


            function clearInsertPlayer() {
                $('#players-search').val('');
                $('#players-search').data('selectedValue', null);
                $('#players-search').data('data-selected-value', null);
                $('#players-search').trigger('change');
            }

            $('#guess-button').on('click', function() {
                let inputBar = $('#players-search');
                let id = $playersSearch.data('data-selected-value');

                if (id === undefined || id === null || id === '') {
                    syncGuessButtonState();
                    return;
                }

                //fetch (give attempt_id e player_id) return true (con quiz answer id) o false (con null)
                fetch('/userAnswer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            attempt_id: quiz_attempt,
                            player_id: id
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            let answer = data.correct_quiz_answer_id;
                            let gameOver = data.game_over;
                            if (answer < 0) { //user answer is wrong
                                if (gameOver == -1) { //game over, loss
                                    document.getElementById('quizTitolo').innerText = 'GAME OVER';
                                    markError(errorCount, data.player_name);
                                    document.body.classList.remove('bodyyellow');
                                    document.body.classList.add('bodyred');
                                    game_over();
                                    reveal_buttons('reveal-button');
                                } else { //user answer is wrong but game not over
                                    markError(errorCount, data.player_name);
                                    errorCount++;
                                }
                            } else { //user answer is correct
                                const answerCard = document.getElementById(String(answer));
                                if (gameOver == 1) { //game over, win
                                    updateCardState(answer, true, false);
                                    document.body.classList.remove('bodyyellow');
                                    document.body.classList.add('bodygreen');
                                    if (answerCard) {
                                        const playerName = answerCard.dataset.player || answerCard.dataset.context || '';
                                        showFeedbackBanner('success', '@lang('messages.guessCorrect')', playerName ? playerName : '');
                                        pulseElement(answerCard);
                                        scrollElementIntoViewIfNeeded(answerCard);
                                    }
                                    game_over();

                                } else { //user answer is correct but game not over
                                    updateCardState(answer, true, false);
                                    if (answerCard) {
                                        const playerName = answerCard.dataset.player || answerCard.dataset.context || '';
                                        showFeedbackBanner('success', '@lang('messages.guessCorrect')', playerName ? playerName : '');
                                        pulseElement(answerCard);
                                        scrollElementIntoViewIfNeeded(answerCard);
                                    }
                                }
                            }
                        } else {
                            alert('Error saving player.');
                        }
                        clearInsertPlayer();
                        syncGuessButtonState();
                    });
            });

            function markError(erCount, playerName, announce = true) {
                const errorIcon = document.getElementById(`error${erCount}`);
                if (errorIcon) {
                    errorIcon.classList.remove('text-white');
                    errorIcon.classList.add('text-danger');
                    errorIcon.setAttribute('data-bs-content', playerName);

                    // Enable the popover (create instance)
                    const popover = bootstrap.Popover.getInstance(errorIcon) || new bootstrap.Popover(errorIcon, {
                        trigger: 'hover',
                        placement: 'top',
                        html: true,
                    });

                    if (announce) {
                        showFeedbackBanner('danger', '@lang('messages.guessWrong')', playerName || '');
                        pulseElement(errorIcon);
                        scrollElementIntoViewIfNeeded(errorIcon);
                    }
                } else {
                    console.warn(`Element with ID "error${erCount}" not found.`);
                }
            }

            function reveal_buttons(id){
                const link = document.getElementById(id);
                link.classList.remove('d-none');
                link.classList.remove('disabled');
                link.removeAttribute('aria-disabled');
                link.removeAttribute('tabindex');
            }

            function game_over() {
                $('#guess-button').remove();
                $('#guess-row').remove();

                reveal_buttons('myButtonLink');

            }


        });

        function revealCards() {
                document.querySelectorAll('.player-card').forEach(card => {
                    let answerId = card.getAttribute('data-answer-id');
                    let active = card.classList.contains(
                        'card-player-active');
                    if (!active) {
                        updateCardState(answerId, false, true);
                    }
                });
            }
    </script>
@endpush
