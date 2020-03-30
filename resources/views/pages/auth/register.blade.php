@extends("layouts.auth")

@section("title")
    - Register account
@stop

@section("content")
    <div class="content-section__wrapper">
        <div class="content-section">

            <div id="register">

                @include("partials.feedback")

                <form action="{{ route('register.post') }}" method="post">
                    @csrf

                    <register-form
                        :errors="{{ $errors->toJson() }}"
                        :old-input="{{ $oldInput->toJson() }}"
                        title-text="@lang('auth.register_title')"
                        username-text="@lang('auth.register_username')"
                        name-text="@lang('auth.register_name')"
                        email-text="@lang('auth.register_email')"
                        password-text="@lang('auth.register_password')"
                        confirm-password-text="@lang('auth.register_confirm_password')"
                        submit-text="@lang('auth.register_submit')"
                        login-text="@lang('auth.register_go_to_login')"
                        login-href="{{ route('login') }}">
                    </register-form>
                
                </form>

            </div>

        </div>
    </div>
@stop