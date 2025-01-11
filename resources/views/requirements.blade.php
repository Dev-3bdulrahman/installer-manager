@extends('installer::layout')

@section('content')
<div class="container">
    <h2>System Requirements</h2>
    
    <div class="requirements-list">
        @foreach($requirements as $requirement => $satisfied)
            <div class="requirement-item {{ $satisfied ? 'satisfied' : 'not-satisfied' }}">
                <span class="requirement-name">{{ $requirement }}</span>
                <span class="requirement-status">
                    @if($satisfied)
                        ✓ Satisfied
                    @else
                        ✗ Not Satisfied
                    @endif
                </span>
            </div>
        @endforeach
    </div>

    @if(collect($requirements)->every(fn($item) => $item))
        <a href="{{ route('installer.database') }}" class="btn btn-primary">
            Continue to Database Setup
        </a>
    @else
        <div class="alert alert-danger">
            Please fix the requirements before continuing.
        </div>
    @endif
</div>
@endsection