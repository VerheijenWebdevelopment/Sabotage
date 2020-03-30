@extends("layouts.auth")

@section("title")
    - Login
@stop

@section("content")
    <div class="content-section__wrapper">
        <div class="content-section">
            
            @include("partials.feedback")

            <div id="login" class="elevation-1">
                

                <form action="{{ route('login.post') }}" method="post">
                    @csrf
                    
                    <login-form
                        :errors="{{ $errors->toJson() }}"
                        :old-input="{{ $oldInput->toJson() }}"
                        title-text="@lang('auth.login_title')"
                        email-text="@lang('auth.login_email')"
                        password-text="@lang('auth.login_password')"
                        remember-me-text="@lang('auth.login_remember_me')"
                        submit-text="@lang('auth.login_submit')"
                        forgot-password-text="@lang('auth.login_forgot_password')"
                        forgot-password-href="{{ route('recover-account') }}"
                        register-text="@lang('auth.login_register')"
                        register-href="{{ route('register') }}">
                    </login-form>

                </form>
            </div>

        </div>
    </div>
@stop