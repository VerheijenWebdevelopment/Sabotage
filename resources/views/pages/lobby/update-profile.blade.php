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
                title-text="@lang('settings.update_profile_title')"
                username-text="@lang('settings.update_profile_username')"
                name-text="@lang('settings.update_profile_name')"
                email-text="@lang('settings.update_profile_email')"
                avatar-text="@lang('settings.update_profile_avatar')"
                cancel-text="@lang('settings.update_profile_cancel')"
                cancel-href="{{ route('settings.profile') }}"
                submit-text="@lang('settings.update_profile_submit')">
            </update-profile-form>

        </form>

    </div>

@stop