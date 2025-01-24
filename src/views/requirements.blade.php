@extends('installer::layout')

@section('content')
<div class="container">
    <h2>{{ __('System Requirements') }}</h2>

    <div class="requirements-list">
        @foreach($requirements as $requirement => $satisfied)
            <div class="requirement-item {{ $satisfied ? 'satisfied' : 'not-satisfied' }}">
                <span class="requirement-name">{{ $requirement }}</span>
                <span class="requirement-status">
                    @if($satisfied)
                        ✓ {{ __('Satisfied') }}
                    @else
                        ✗ {{ __('Not Satisfied') }}
                    @endif
                </span>
            </div>
        @endforeach
    </div>

    @if(collect($requirements)->every(fn($item) => $item))
    <div class="requirements-ok m-auto text-center">
        <a href="{{ route('installer.database') }}" class="btn btn-success mt-4">
            {{ __('Continue to Database Setup') }}
        </a>
        </div>
    @else
        <div class="alert alert-danger mt-4">
            {{ __('Please fix the requirements before continuing.') }}
        </div>
    @endif
</div>
@endsection
