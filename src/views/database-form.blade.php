@extends('installer::layout')

@section('content')
<div class="container">
    <h2>{{ __('Database Configuration') }}</h2>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('installer.database.save') }}">
        @csrf
        <div class="form-group">
            <label for="db_host">{{__('Database Host')}}</label>
            <input type="text" name="db_host" id="db_host" class="form-control"
                   value="{{ old('db_host', 'localhost') }}" required>
        </div>

        <div class="form-group">
            <label for="db_name">{{__('Database Name')}}</label>
            <input type="text" name="db_name" id="db_name" class="form-control"
                   value="{{ old('db_name') }}" required>
        </div>

        <div class="form-group">
            <label for="db_user">{{__('Database Username')}}</label>
            <input type="text" name="db_user" id="db_user" class="form-control"
                   value="{{ old('db_user') }}" required>
        </div>

        <div class="form-group">
            <label for="db_password">{{__('Database Password')}}</label>
            <input type="password" name="db_password" id="db_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">{{__('Configure Database')}}</button>
    </form>
</div>
@endsection
