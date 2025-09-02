<!DOCTYPE html>
<html>

<head>
    <title>@yield('title')</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ url('/') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('img/icon.jpg') }}">

    <!-- Bootstrap 5 JS bundle (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Optional: jQuery and typeahead (only if you're actually using them) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.3.1/typeahead.bundle.min.js"></script>



</head>

<body class="bodyblue d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light navbarColor">
        <div class="container-fluid navbarColor">

            <!-- logo come link home -->
            <a class="navbar-brand nav-link @yield('active_home')" href="{{ route('home') }}">
                <img src="{{ asset('img/logo.png') }}" width="35" height="35"
                    class="d-inline-block align-top img-thumbnail-navbar" alt="">
            </a>

            <!-- Button per il menu mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu di navigazione -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <!-- lista di navigazione sinistra -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    @if (auth()->check())
                        <li class="nav-item me-3">
                            <a class="nav-link" href="{{ route('quiz.index') }}">@lang('messages.play')</a>
                        </li>

                        @if (auth()->user()->role == 'creator')
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('creator.statistics') }}">@lang('messages.stats')</a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('create.quiz') }}">@lang('messages.create_quiz')</a>
                            </li>
                        @elseif (auth()->user()->role == 'admin')
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('user.statistics') }}">@lang('messages.stats')</a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('approve.quiz') }}">@lang('messages.approve_quiz')</a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('manage.database') }}">@lang('messages.manage_database')</a>
                            </li>
                        @else
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('user.statistics') }}">@lang('messages.stats')</a>
                            </li>
                        @endif

                    @endif
                </ul>

                <!-- lista di navigazione destra -->
                <ul class="navbar-nav">

                    <li class="nav-item dropdown me-4">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate fs-5"></i>
                        </a>
                        <ul class="dropdown-menu auto-width" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('setLang', ['lang' => 'en']) }}">
                                    <img src="{{ url('/') }}/img/flags/en.png" width="35" height="25"
                                        class="d-inline-block align-top" />
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('setLang', ['lang' => 'it']) }}">
                                    <img src="{{ url('/') }}/img/flags/it.png" width="35" height="25"
                                        class="d-inline-block align-top" />
                                </a>
                            </li>
                        </ul>
                    </li>


                    @if (auth()->check())
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">
                                    @lang('messages.logout')
                                </button>
                            </form>

                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">@lang('messages.login')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">@lang('messages.signup')</a>
                        </li>
                    @endif


                </ul>

            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @yield('body')
    </main>

    <footer class="bg-light text-center text-lg-start mt-5 border-top">
        <div class="container py-3">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-6 text-md-start mb-2 mb-md-0">
                    <span class="text-muted">&copy; 2025 Soccer Quiz</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="" class="text-decoration-none text-muted me-3">Privacy</a>
                    <a href="" class="text-decoration-none text-muted">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>




</html>
