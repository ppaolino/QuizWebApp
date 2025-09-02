@extends('layouts.master')
@section('active_home', 'active')

@section('body')
    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <h1>@lang('messages.approve_quiz')</h1>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('messages.quiz_name')</th>
                                <th>@lang('messages.prompt')</th>
                                <th>@lang('messages.max_errors')</th>
                                <th>@lang('messages.creator_name')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quiz as $q)
                                <tr>
                                    <td>{{ $q->name }}</td>
                                    <td>{{ $q->prompt_text }}</td>
                                    <td>{{ $q->max_errors }}</td>
                                    <td>{{ $q->creator_name }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <form class="approve-form me-2" data-id="{{ $q->id }}">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-outline-success btn-approve">
                                                    @lang('messages.approve')
                                                </button>
                                            </form>
                                            <form class="reject-form" data-id="{{ $q->id }}">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-reject">
                                                    @lang('messages.reject')
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if (count($quiz) === 0)
                                <tr>
                                    <td colspan="5" class="text-center">@lang('messages.no_quiz_to_approve')</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.btn-approve', function() {
            const form = $(this).closest('.approve-form');
            const quizId = form.data('id');
            $.ajax({
                url: `/approve/quiz/${quizId}`,
                type: 'PUT',
                data: {
                    _token: form.find('input[name="_token"]').val(),
                    id: quizId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error approving quiz.');
                }
            });
        });

        $(document).on('click', '.btn-reject', function() {
            const form = $(this).closest('.reject-form');
            const quizId = form.data('id');
            $.ajax({
                url: `/reject/quiz/${quizId}`,
                type: 'DELETE',
                data: {
                    _token: form.find('input[name="_token"]').val(),
                    id: quizId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error rejecting quiz.');
                }
            });
        });
    </script>
@endpush
