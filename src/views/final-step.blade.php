@extends('installer::layout')

@section('content')
    <div class="container text-center">
        <div class="card text-center">
            <div class="card-header">
                {{ __('The End') }}
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ __('I happy to helping you to install the  laravel application') }}</h5>
                <p class="card-text">{{ __('If you find this package helpful, please consider giving it a star on GitHub') }}</p>
                <p class="card-text">{{ __('This Package is under development to add more features') }}</p>
                <p class="card-text">{{ __('There are other paid packages to install some premium features') }}</p>
                <a target="_blank" href="https://3bdulrahman.com" class="btn btn-primary">{{ __('Visit my website') }}</a>
            </div>
            <div class="card-footer text-muted">
                {{ __('best regards') }}
            </div>
        </div>

    </div>
@endsection
