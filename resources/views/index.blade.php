@extends('layouts.master')

@section('active_home', 'active')

@section('body')
    <div class="container-fluid px-0">
        <!-- Hero Section -->
        <div class="row justify-content-center align-items-center py-5 mb-4">
            <div class="col-md-3 text-center">
                <img src="{{ asset('img/logo.png') }}" alt="Soccer Quiz Logo" class="img-fluid mb-3 img-index">
            </div>
            <div class="col-md-7 text-center">
                <h1 class="display-4 fw-bold">Soccer Quiz WebApp</h1>
                <p class="lead"> @lang('messages.indexPhrase')</p>
                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                    <a href="{{ route('quiz.index') }}" class="btn btn-success btn-lg">
                        <i class="bi bi-controller"></i> @lang('messages.play')
                    </a>
                </div>
            </div>
        </div>

        <!-- Carousel Section: full-width, proportional height -->
        <div class="row  g-0 mb-5">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <div id="homepageCarousel" class="carousel slide shadow rounded-pill" data-bs-ride="carousel"
                    data-bs-interval="2000" style="width:75%;">
                    <div class="carousel-inner ratio ratio-21x9">
                        <div class="carousel-item active">
                            <img src="{{ asset('img/homepage/img1.jpg') }}" class="d-block w-100 h-100" alt="Football 1"
                                style="object-fit:cover;">
                            <div class="carousel-caption  bg-dark bg-opacity-50 rounded">
                                <h5>@lang('messages.carousel1')</h5>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('img/homepage/img2.jpg') }}" class="d-block w-100 h-100" alt="Football 2"
                                style="object-fit:cover;">
                            <div class="carousel-caption  bg-dark bg-opacity-50 rounded">
                                <h5>@lang('messages.carousel2')</h5>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('img/homepage/img4.jpg') }}" class="d-block w-100 h-100" alt="Football 3"
                                style="object-fit:cover;">
                            <div class="carousel-caption  bg-dark bg-opacity-50 rounded">
                                <h5>@lang('messages.carousel3')</h5>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('img/homepage/img3.jpg') }}" class="d-block w-100 h-100" alt="Football 4"
                                style="object-fit:cover;">
                            <div class="carousel-caption  bg-dark bg-opacity-50 rounded">
                                <h5>@lang('messages.carousel4')</h5>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#homepageCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#homepageCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <div class="row text-center g-4">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <i class="bi bi-trophy-fill fs-1 text-warning mb-3"></i>
                                <h5 class="card-title">@lang('messages.feature1')</h5>
                                <p class="card-text">@lang('messages.featureDesc1')</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <i class="bi bi-lightbulb-fill fs-1 text-info mb-3"></i>
                                <h5 class="card-title">@lang('messages.feature2')</h5>
                                <p class="card-text">@lang('messages.featureDesc2')
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <i class="bi bi-people-fill fs-1 text-success mb-3"></i>
                                <h5 class="card-title">@lang('messages.feature3')</h5>
                                <p class="card-text">@lang('messages.featureDesc3')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!auth()->check())
            <!-- Call to Action Section -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-8 text-center">
                    <h2 class="fw-bold mb-3">@lang('messages.calltoaction1')</h2>
                    <p class="mb-4">@lang('messages.calltoaction2')</p>
                    <a href="{{ route('register') }}" class="btn btn-lg btn-warning">
                        <i class="bi bi-person-plus"></i>@lang('messages.calltoaction3')
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
