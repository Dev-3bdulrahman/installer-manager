@extends('installer::layout')

@section('content')
    <div class="container text-center">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h2>{{ __('Congratulations!') }}</h2>
                <p class="lead"><strong>{{ __('Welcome message') }}</strong></p>

                <p class="lead">{{ __('Structure message') }}</p>
                <div class="mb-4">
                    <label for="languageSelect">{{ __('Select Language') }}</label>
                    <select class="form-select" id="languageSelect" onchange="changeLanguage(this.value)">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                        <option value="ar" {{ app()->getLocale() == 'ar' ? 'selected' : '' }}>{{ __('Arabic') }}</option>
                    </select>
                </div>

                <script>
                    function changeLanguage(lang) {
                        window.location.href = "{{ url('install/language') }}/" + encodeURIComponent(lang);
                    }
                </script>
                <div class="mt-4">
                    <a href="{{ route('installer.requirements') }}" class="btn btn-success ml-2" id="nextBtn" disabled>{{ __('next') }}</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('migrateBtn').addEventListener('click', function() {
            document.getElementById('nextBtn').removeAttribute('disabled');
        });
    </script>
@endsection
