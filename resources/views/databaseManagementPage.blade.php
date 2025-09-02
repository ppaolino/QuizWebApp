@extends('layouts.master')
@section('active_home', 'active')

@section('body')
    <div class="container-fluid mb-5">
        <div class="row justify-content-center mt-5 mb-4">
            <div class="col-lg-4 col-md-6 col-12 text-center">
                <h3>@lang('messages.playersDatabase')</h3>
                <hr class="mb-4">
                <h5 class="mb-3">@lang('messages.insertPlayer')</h5>
                <form id="addPlayerForm" enctype="multipart/form-data" class="mb-4 text-center">
                    @csrf
                    <div class="mb-2">
                        <label for="playerName" class="form-label">@lang('messages.playerName')</label>
                        <input type="text" class="form-control" id="playerName" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label for="playerPosition" class="form-label">@lang('messages.playerPosition')</label>
                        <select class="form-control" id="playerPosition" name="position" required>
                            <option value="G">G</option>
                            <option value="D">D</option>
                            <option value="M">M</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" id="addPlayerBtn">@lang('messages.addPlayer')</button>
                </form>
                <hr class="mb-4">
                <h5 class="mb-3">@lang('messages.modifyPlayer')</h5>
                <x-bar-search type="players" />
                <form id="editPlayerForm" class="mt-2 mb-4 text-center">
                    @csrf
                    <input type="hidden" id="editPlayerId" name="id">
                    <div class="mb-2">
                        <label for="editPlayerName" class="form-label">@lang('messages.playerName')</label>
                        <input type="text" class="form-control" id="editPlayerName" name="name">
                    </div>
                    <div class="mb-2">
                        <label for="editPlayerPosition" class="form-label">@lang('messages.playerPosition')</label>
                        <select class="form-control" id="editPlayerPosition" name="position">
                            <option value="G">G</option>
                            <option value="D">D</option>
                            <option value="M">M</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-warning" id="editPlayerBtn">@lang('messages.modifyPlayer')</button>
                </form>
            </div>
        </div>
    </div>
    <div class="container-fluid mb-5">
        <div class="row justify-content-center mt-5 mb-4">
            <div class="col-lg-4 col-md-6 col-12 text-center">
                <h3>@lang('messages.teamsDatabase')</h3>
                <hr class="mb-4">
                <h5 class="mb-3">@lang('messages.insertTeam')</h5>
                <form id="addTeamForm" enctype="multipart/form-data" class="mb-4 text-center">
                    @csrf
                    <div class="mb-2">
                        <label for="teamName" class="form-label">@lang('messages.teamName')</label>
                        <input type="text" class="form-control" id="teamName" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label for="teamLeagueId" class="form-label">@lang('messages.chooseLeague')</label>
                        <select class="form-control" id="teamLeagueId" name="leagueId">
                            <option value="">@lang('messages.selectLeague')</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="teamLogo" class="form-label">@lang('messages.teamLogo')</label>
                        <input type="file" class="form-control" id="teamLogo" name="logo" accept=".png">
                    </div>
                    <button type="button" class="btn btn-primary" id="addTeamBtn">@lang('messages.addTeam')</button>
                </form>
                <hr class="mb-4">
                <h5 class="mb-3">@lang('messages.modifyTeam')</h5>
                <x-bar-search type="teams" />
                <form id="editTeamForm" class="mt-2 mb-4 text-center" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editTeamId" name="id">
                    <div class="mb-2">
                        <label for="editTeamName" class="form-label">@lang('messages.teamName')</label>
                        <input type="text" class="form-control" id="editTeamName" name="name">
                    </div>
                    <div class="mb-2">
                        <label for="editTeamLogo" class="form-label">@lang('messages.teamLogo')</label>
                        <input type="file" class="form-control" id="editTeamLogo" name="logo" accept=".png">
                    </div>
                    <button type="button" class="btn btn-warning" id="editTeamBtn">@lang('messages.modifyTeam')</button>
                </form>
            </div>
        </div>
    </div>
    <div class="container-fluid mb-5">
        <div class="row justify-content-center mt-5 mb-4">
            <div class="col-lg-4 col-md-6 col-12 text-center">
                <h3>@lang('messages.leaguesDatabase')</h3>
                <hr class="mb-4">
                <h5 class="mb-3">@lang('messages.insertLeague')</h5>
                <form id="addLeagueForm" enctype="multipart/form-data" class="mb-4 text-center">
                    @csrf
                    <div class="mb-2">
                        <label for="leagueName" class="form-label">@lang('messages.leagueName')</label>
                        <input type="text" class="form-control" id="leagueName" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label for="leagueLogo" class="form-label">@lang('messages.leagueLogo')</label>
                        <input type="file" class="form-control" id="leagueLogo" name="logo" accept=".png">
                    </div>
                    <button type="button" class="btn btn-primary" id="addLeagueBtn">@lang('messages.addLeague')</button>
                </form>
                <hr class="mb-4">
                <h5 class="mb-3">@lang('messages.modifyLeague')</h5>
                <x-bar-search type="leagues" />
                <form id="editLeagueForm" class="mt-2 mb-4 text-center" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editLeagueId" name="id">
                    <div class="mb-2">
                        <label for="editLeagueName" class="form-label">@lang('messages.leagueName')</label>
                        <input type="text" class="form-control" id="editLeagueName" name="name">
                    </div>
                    <div class="mb-2">
                        <label for="editLeagueLogo" class="form-label">@lang('messages.leagueLogo')</label>
                        <input type="file" class="form-control" id="editLeagueLogo" name="logo" accept=".png">
                    </div>
                    <button type="button" class="btn btn-warning" id="editLeagueBtn">@lang('messages.modifyLeague')</button>
                </form>
            </div>
        </div>
    </div>
    @stack('scripts')
@endsection

@push('scripts')
    <script>
        // Player Add
        $('#addPlayerBtn').on('click', function() {
            $.ajax({
                url: '/player',
                type: 'POST',
                data: $('#addPlayerForm').serialize(),
                success: function(response) {
                    alert(response.success ? 'Player added!' : 'Error adding player.');
                    if (response.success) location.reload();
                }
            });
        });

        // Player Edit
        $('#editPlayerBtn').on('click', function() {
            const id = $('#editPlayerId').val();
            $.ajax({
                url: `/player/${id}`,
                type: 'PUT',
                data: $('#editPlayerForm').serialize(),
                success: function(response) {
                    alert(response.success ? 'Player updated!' : 'Error updating player.');
                    if (response.success) location.reload();
                }
            });
        });

        // Team Add
        $('#addTeamBtn').on('click', function() {
            let form = $('#addTeamForm')[0];
            let formData = new FormData(form);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            $.ajax({
                url: '/team',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response.success ? 'Team added!' : 'Error adding team.');
                    if (response.success) location.reload();
                }
            });
        });

        // Team Edit
        $('#editTeamBtn').on('click', function() {
            const id = $('#editTeamId').val();
            let form = $('#editTeamForm')[0];
            let formData = new FormData(form);
            formData.append('id', id);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            $.ajax({
                url: `/team/${id}`,
                type: 'POST', // Laravel expects POST for file upload, with _method=PUT
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                success: function(response) {
                    alert(response.success ? 'Team updated!' : 'Error updating team.');
                    if (response.success) location.reload();
                }
            });
        });

        // League Add
        $('#addLeagueBtn').on('click', function() {
            const form = $('#addLeagueForm')[0];
            const formData = new FormData(form);

            $.ajax({
                url: '/league', // POST /league (Route::resource)
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function(response) {
                    alert(response.success ? 'League added!' : 'Error adding league.');
                    if (response.success) location.reload();
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error adding league.';
                    alert(msg);
                }
            });
        });


        // League Edit
        $('#editLeagueBtn').on('click', function() {
            const id = $('#editLeagueId').val();
            let form = $('#editLeagueForm')[0];
            let formData = new FormData(form);
            formData.append('id', id);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            $.ajax({
                url: `/league/${id}`,
                type: 'POST', // Laravel expects POST for file upload, with _method=PUT
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                success: function(response) {
                    alert(response.success ? 'League updated!' : 'Error updating league.');
                    if (response.success) location.reload();
                }
            });
        });

        // Populate league select for teams
        $(document).ready(function() {
            $.get('/league', function(leagues) {
                const $leagueSelect = $('#teamLeagueId');
                $leagueSelect.empty();
                $leagueSelect.append(`<option value="">@lang('messages.selectLeague')</option>`);
                leagues.forEach(function(league) {
                    $leagueSelect.append(`<option value="${league.id}">${league.name}</option>`);
                });
            });
        });

        // Bar-search select handlers
        $('#players-search').on('typeahead:select', function(ev, suggestion) {
            $('#editPlayerId').val(suggestion.id);
            $('#editPlayerName').val(suggestion.name);
            $('#editPlayerPosition').val(suggestion.position);
        });
        $('#teams-search').on('typeahead:select', function(ev, suggestion) {
            $('#editTeamId').val(suggestion.id);
            $('#editTeamName').val(suggestion.name);
        });
        $('#leagues-search').on('typeahead:select', function(ev, suggestion) {
            $('#editLeagueId').val(suggestion.id);
            $('#editLeagueName').val(suggestion.name);
        });
    </script>
@endpush
