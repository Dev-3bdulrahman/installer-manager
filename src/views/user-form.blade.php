@extends('installer::layout')

@section('content')
<div class="container">
    <h2>{{ __('Super Admin') }}</h2>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('installer.userdata.save') }}">
        @csrf

        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input type="email" name="email" id="email" class="form-control"
            value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </form>
</div>
@endsection
