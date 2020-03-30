@extends("layouts.auth")

@section("title")
    - Reset your password
@stop

@section("content")
    <div class="content-section__wrapper">
        <div class="content-section">

            <div id="reset-password">
            
                @include("partials.feedback")

                <form action="{{ route('reset-password.post', ['email' => $email, 'code' => $code]) }}" method="post">
                    @csrf

                    <reset-password-form
                        code="{{ $code }}"
                        email="{{ $email }}"
                        :errors="{{ $errors->toJson() }}"
                        title-text="@lang('auth.reset_title')"
                        email-label-text="@lang('auth.reset_email')"
                        code-label-text="@lang('auth.reset_code')"
                        password-label-text="@lang('auth.reset_password')"
                        password-confirmation-label-text="@lang('auth.reset_confirm_password')"
                        back-href="{{ route('login') }}"
                        back-text="@lang('auth.reset_back')"
                        submit-text="@lang('auth.reset_submit')">
                    </reset-password-form>

                </form>

            </div>

        </div>
    </div>
@stop