@extends('layouts.master')
@section('active_home', 'active')

@section('body')


    <div class="container-fluid">
        <div class="row d-flex justify-content-center g-0 text-center mb-3  mt-5">
            <div class="col-md-12">
                <h1 id="title">@lang('messages.create_quiz')</h1>
                <p id="initialMessage">@lang('messages.chooseTitle')</p>
            </div>
        </div>

        @csrf
        <form id="form-titolo">
            <div class="row d-flex justify-content-center g-0 text-center mb-3 mt-5">
                <div class="col-md-2">
                    <div class="dropdown" id="languageSelector">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" id="dropdownBtn">
                            <img src="{{ url('/img/flags/en.png') }}" width="20" height="20" class="selected-flag" />
                            <span class="selected-language">English</span>
                        </button>
                        <ul class="dropdown-menu w-100">
                            @foreach ($languages as $language)
                                <li>
                                    <a class="dropdown-item language-option" href="#"
                                        data-lang-abbr="{{ $language['abbr'] }}" data-lang-name="{{ $language['name'] }}">
                                        <img src="{{ url('/img/flags/' . $language['abbr'] . '.png') }}" width="20"
                                            height="20" />
                                        {{ $language['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="language" id="selectedLanguage" value="en">
                    </div>
                </div>
            </div>
            <div class="row  d-flex justify-content-center align-items-center ">
                <div class="col-md-6 mb-3">
                    <input type="text" class="form-control" id="titleInput" placeholder="Enter title" name="title">
                </div>
            </div>
            <div class="row  d-flex justify-content-center align-items-center ">
                <div class="col-md-6 mb-3">
                    <button type="submit" class="btn btn-outline-secondary" id="verifyButton">@lang('messages.verify')</button>
                </div>
            </div>
        </form>


        <form class="form" id="quiz-form">
            <div class="row mb-3  mt-5">
                <div class="col-lg-1 form-group mb-2">
                    <label for="prompt">@lang('messages.description')</label>
                </div>
                <div class="col-lg-8 form-group mb-2">
                    <textarea class="form-control" id="prompt" rows="4"></textarea>
                </div>
            </div>
            <div class="row mb-3  mt-5">
                <div class="col-lg-1 form-group mb-2">
                    <label for="max_errors">@lang('messages.errorsAllowed')</label>
                </div>
                <div class="col-sm-2 form-group mb-2">
                    <select class="form-control" id="max_errors">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="row mb-3  mt-5">
            <div class="col-lg-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#insertPlayer"
                    id="insertPlayerButton">
                    @lang('messages.insertAnswer')
                </button>
            </div>
        </div>

        <div class="modal fade" id="insertPlayer" tabindex="-1" role="dialog" aria-labelledby="inserPlayerLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inserPlayerLabel">@lang('messages.insertPlayer')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="insertPlayerForm">
                            <div class="row d-flex justify-content-center align-items-center mb-3">

                                <div class="form-group">
                                    <div class="col-12 col-lg-6">
                                        <label for="players-search" class="col-form-label">@lang('messages.selectCorrectAnswer')</label>
                                    </div>
                                    <x-bar-search type="players" />
                                </div>

                            </div>
                            <div class="row d-flex justify-content-center align-items-center mb-3">
                                <div class="form-group">
                                    <div class="col-12 col-lg-6">
                                        <label for="recipient-name" class="col-form-label">@lang('messages.insertAnswerDescription')</label>
                                    </div>
                                    <div class="col-12 col-lg-6 mx-auto">
                                        <input type="text" class="form-control" id="context_info"
                                            placeholder="e.g. year | number of ...">
                                    </div>
                                </div>
                            </div>
                            <label class="col-form-label">@lang('messages.selectSuggestionType')</label>
                            <div class="row d-flex justify-content-center align-items-center mb-3">
                                <div class="form-group">

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                            value="team">
                                        <label class="form-check-label" for="inlineCheckbox1">@lang('messages.suggestionTypeTeam')</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                            value="league">
                                        <label class="form-check-label" for="inlineCheckbox2">@lang('messages.suggestionTypeLeague')</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                            value="nothing">
                                        <label class="form-check-label" for="inlineCheckbox3">@lang('messages.suggestionTypeNone')</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <x-bar-search type="teams" />
                                <x-bar-search type="leagues" />
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('messages.close')</button>

                        <button type="button" class="btn btn-light" id="savePlayer">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mb-3 mt-2" id="component-container"></div>

        <div class="row d-flex justify-content-center g-0 text-center mb-3  mt-2">
            <div class="col-lg-6">
                <button type="button" class="btn btn-primary" id="finalSave">
                    @lang('messages.confirm')
                </button>
                <button type="button" class="btn btn-danger" id="deleteQuiz">
                    @lang('messages.deleteDraft')
                </button>
            </div>
        </div>

    </div>


    @stack('scripts')
@endsection
@push('scripts')
    <script>
        const quiz = @json($quiz);
        if (quiz) {
            $('#title').text(quiz.name); // Update page title
            const finishPreviousMsg = @json(__('messages.initialMessage'));
            $('#initialMessage').text(finishPreviousMsg);
            $('#form-titolo').hide(); // Hide the form
            $('#prompt').val(quiz.prompt_text); // Set description if available
            $('#max_errors').val(quiz.max_errors); // Set max errors

            //cards
            const answers = @json($cards);

            answers.forEach(element => {
                const wrapper = $('<div>', {
                    class: 'col col-lg-2 col-md-3 col-sm-4 col-xs-4 d-flex justify-content-center align-items-center mb-3',
                    html: element
                });
                $('#component-container').append(wrapper);
            });

        } else {
            $('#quiz-form').hide();
            $('#insertPlayerButton').hide();
            $('#finalSave').hide();
            $('#deleteQuiz').hide();


        }
        $(document).ready(function() {

            const languageSelector = document.getElementById('languageSelector');

            // Handle language selection
            languageSelector.querySelectorAll('.language-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();

                    const langAbbr = this.dataset.langAbbr;
                    const langName = this.dataset.langName;
                    const flagSrc = this.querySelector('img').src;

                    // Update display
                    languageSelector.querySelector('.selected-flag').src = flagSrc;
                    languageSelector.querySelector('.selected-language').textContent =
                        langName;

                    // Update hidden input
                    document.getElementById('selectedLanguage').value = langAbbr;
                });
            });

            // Function to get selected value anytime
            function getSelectedLanguage() {
                return {
                    abbr: document.getElementById('selectedLanguage').value,
                    name: languageSelector.querySelector('.selected-language').textContent
                };
            }

            // Example usage:
            // const currentLang = getSelectedLanguage();
            // console.log(currentLang.abbr, currentLang.name);


            $('#deleteQuiz').click(function() {
                $.ajax({
                    url: '/quiz/',
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Optionally refresh or remove the quiz from the DOM
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });

            });

            document.getElementById('form-titolo').addEventListener('submit', function(e) {
                e.preventDefault(); // evita reload pagina

                const titolo = document.getElementById('titleInput').value;
                const lang = getSelectedLanguage();

                fetch('/quiz-available', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            title: titolo,
                            language: lang.abbr
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.available) {
                            const titleAvailable = @json(__('messages.titleAvailable'));
                            $('#initialMessage').text(titleAvailable);
                            $('#title').text(titolo); // Aggiorna il titolo della pagina
                            $('#form-titolo').hide();
                            $(this).data('quizId', data.quiz_id);
                            $('#quiz-form').show();
                            $('#insertPlayerButton').show();
                            $('#finalSave').show();
                            $('#deleteQuiz').show();
                        } else {
                            alert('Titolo già usato, scegline un altro.');
                        }
                    });
            });
            // Cache jQuery objects
            const $checkboxTeam = $('#inlineCheckbox1');
            const $checkboxLeague = $('#inlineCheckbox2');
            const $checkboxNothing = $('#inlineCheckbox3');

            const $playersSearch = $('#players-search');
            const $teamsSearch = $('#teams-search');
            const $leaguesSearch = $('#leagues-search');

            const $contextInfo = $('#context_info');
            const $savePlayerButton = $('#savePlayer');

            function updateVisibility() {
                if ($checkboxTeam.is(':checked')) {
                    $teamsSearch.show();
                    $leaguesSearch.hide();
                    $checkboxLeague.prop('checked', false);
                    $checkboxNothing.prop('checked', false);
                } else if ($checkboxLeague.is(':checked')) {
                    $leaguesSearch.show();
                    $teamsSearch.hide();
                    $checkboxTeam.prop('checked', false);
                    $checkboxNothing.prop('checked', false);
                } else if ($checkboxNothing.is(':checked')) {
                    $teamsSearch.hide();
                    $leaguesSearch.hide();
                    $checkboxTeam.prop('checked', false);
                    $checkboxLeague.prop('checked', false);
                } else {
                    // If none selected, hide all
                    $teamsSearch.hide();
                    $leaguesSearch.hide();
                }
            }

            function save_player() {
                const playerId = $playersSearch.data('data-selected-value');

                if (!playerId) {
                    alert('Please select a player.');
                    return;
                }

                const context = $contextInfo.val();

                if (!context) {
                    alert('Please provide context information.');
                    return;
                }

                let team = null;
                let league = null;

                if ($checkboxTeam.is(':checked')) {
                    team = $teamsSearch.data('data-selected-value');
                }
                if ($checkboxLeague.is(':checked')) {
                    league = $leaguesSearch.data('data-selected-value');
                }

                if (!team && !league && !$checkboxNothing.is(':checked')) {
                    alert('Please select at least one type of suggestion.');
                    return;
                }

                fetch('/quiz_answer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            playerId: playerId,
                            context: context,
                            team: team,
                            league: league
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {

                            const wrapper = $('<div>', {
                                class: 'col col-lg-2 col-md-3 col-sm-4 col-xs-4 d-flex justify-content-center align-items-center mb-3',
                                html: data.html
                            });
                            $('#component-container').append(wrapper);
                            closeMyModal();
                        } else {
                            alert('Error saving player.');
                        }
                    });
            }

            function save_quiz() {
                const prompt_text = $('#prompt').val();

                if (!prompt_text) {
                    alert('Please insert the context info.');
                    return;
                }

                const max_errors = $('#max_errors').val();

                if (!max_errors) {
                    alert('Please a number of errors.');
                    return;
                }

                fetch('/quiz', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            context: prompt_text,
                            max_errors: max_errors
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Parse JSON body
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Updated successfully!');
                            window.location.href = data.redirect_url; // Redirect client-side
                        } else {
                            alert('Update failed.');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('Error during request.');
                    });


            }



            // Event listeners
            $checkboxTeam.on('change', updateVisibility);
            $checkboxLeague.on('change', updateVisibility);
            $checkboxNothing.on('change', updateVisibility);
            $savePlayerButton.on('click', save_player);
            $('#finalSave').on('click', save_quiz);

            // Initial state
            updateVisibility();
        });

        function closeMyModal() {
            clearInsertPlayerForm();
            $('#insertPlayer').find('[data-bs-dismiss="modal"]').trigger('click');
        }

        function clearInsertPlayerForm() {
            // Clear the search inputs (assuming these are from x-bar-search components)
            $('#players-search').val('');
            $('#teams-search').val('');
            $('#leagues-search').val('');

            $('#teams-search').hide();
            $('#leagues-search').hide();

            // Clear the context info input
            $('#context_info').val('');

            // Uncheck all checkboxes
            $('#insertPlayerForm input[type="checkbox"]').prop('checked', false);

            // If you need to clear any selected items from the search components,
            // you might need additional code depending on how x-bar-search works
            // For example, if they maintain selected items in some hidden fields or data attributes:
            // $('.search-selected-item').remove();
            // $('[data-selected-id]').removeAttr('data-selected-id');

            // Optional: Reset any select elements if they exist
            $('#insertPlayerForm select').val('');

            // Optional: Trigger change events if needed
            $('#players-search').trigger('change');
        }
    </script>
@endpush
