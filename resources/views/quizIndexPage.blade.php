@extends('layouts.master') <!-- title - active_home - active_MyLibrary - breadcrumb - body -->

@section('active_home', 'active')

@section('body')


    <div class="container-fluid">
        <div class="row justify-content-center g-0 text-center mb-3  mt-5">
            <div class="col-md-12">
                <h1>@lang('messages.startQuiz')</h1>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li><span class="badge" style="background-color: #0d6efd;">&nbsp;</span> @lang('messages.legendBlue')</li>
                    <li><span class="badge" style="background-color: #ffc107;">&nbsp;</span> @lang('messages.legendYellow')</li>
                    <li><span class="badge" style="background-color: #198754;">&nbsp;</span> @lang('messages.legendGreen')</li>
                    <li><span class="badge" style="background-color: #dc3545;">&nbsp;</span> @lang('messages.legendRed')</li>
                </ul>
            </div>
        </div>

        <div class="row justify-content-center g-0 text-center mb-3 mt-5">

            @foreach ($quizzes as $quiz)
                @php
                    switch ($quiz->state) {
                        case -1:
                            $cardColorClass = 'bg-primary';
                            $cardBodyClass = 'quiz-card-body-primary';
                            $route = '/game/' . Hashids::encode($quiz->id) . '/start';
                            $method = 'POST';
                            break;
                        case 0:
                            $cardColorClass = 'bg-warning';
                            $cardBodyClass = 'quiz-card-body-warning';
                            $route = '/game/' . Hashids::encode($quiz->id) . '/play';
                            $method = 'GET';
                            break;
                        case 1:
                            $cardColorClass = 'bg-success';
                            $cardBodyClass = 'quiz-card-body-success';
                            $route = '/game/' . Hashids::encode($quiz->id) . '/start';
                            $method = 'POST';
                            break;
                        case 2:
                            $cardColorClass = 'bg-danger';
                            $cardBodyClass = 'quiz-card-body-danger';
                            $route = '/game/' . Hashids::encode($quiz->id) . '/start';
                            $method = 'POST';
                            break;
                        default:
                            $cardColorClass = 'bg-secondary'; // fallback
                            $cardBodyClass = 'quiz-card-body-primary';
                            $route = '/game/' . Hashids::encode($quiz->id) . '/start';
                            $method = 'POST';
                    }
                @endphp

                <div class="card {{ $cardColorClass }} m-2" style="width: 18rem;">
                    <h5 class="card-header text-white">
                        <img src="{{ url('/') }}/img/flags/{{ $quiz->language_code }}.png"
                            alt="{{ $quiz->language_code }} flag" style="width: 1.2em;">
                        {{ $quiz->name }}

                    </h5>
                    <div class="card-body {{ $cardBodyClass }}">
                        <p class="card-text">{{ $quiz->prompt_text }}</p>
                    </div>
                    <div class="card-footer">
                        <form action="{{ $route }}" method="{{ $method }}">
                            @csrf
                            <button type="submit" class="btn btn-light">@lang('messages.play')</button>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

@endsection
