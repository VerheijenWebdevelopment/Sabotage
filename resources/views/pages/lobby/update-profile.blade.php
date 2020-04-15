@extends("layouts.lobby")

@section("title")
    - Update profile
@stop

@section("content")

    <!-- Header -->
    <div id="page-header">
        <h1 id="page-header__title">
            @lang("settings.update_profile_title")
        </h1>
    </div>

    <!-- Update profile -->
    <div id="update-profile">
        
        <!-- Form -->
        <form action="{{ route('settings.update-profile.post') }}" method="post" enctype="multipart/form-data">
            @csrf

            <update-profile-form
                :user="{{ $user->toJson() }}"
                :errors="{{ $errors->toJson() }}"
                :old-input="{{ $oldInput->toJson() }}"
                :avatars="{{ $avatars->toJson() }}"
                :strings="{{ $strings->toJson() }}"
                base-url="{{ asset('/') }}"
                cancel-href="{{ route('settings.profile') }}">
            </update-profile-form>

        </form>

    </div>

@stop