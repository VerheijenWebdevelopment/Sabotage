@extends("layouts.lobby")

@section("title")
    - Change password
@stop

@section("content")

    <!-- Header -->
    <div id="page-header">
        <h1 id="page-header__title">
            @lang("settings.change_password_title")
        </h1>
    </div>

    <!-- Update profile -->
    <div id="change-password">

        <!-- Feedback -->
        @include("partials.feedback")

        <!-- Form -->
        <form action="{{ route('settings.change-password.post') }}" method="post" enctype="multipart/form-data">
            @csrf

            <change-password-form
                :errors="{{ $errors->toJson() }}"
                password-text="@lang('settings.change_password_password')"
                new-password-text="@lang('settings.change_password_new_password')"
                confirm-new-password-text="@lang('settings.change_password_confirm_new_password')"
                cancel-text="@lang('settings.change_password_cancel')"
                cancel-href="{{ route('settings') }}"
                submit-text="@lang('settings.change_password_submit')">
            </change-password-form>

        </form>

    </div>

@stop