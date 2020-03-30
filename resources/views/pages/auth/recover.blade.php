@extends("layouts.auth")

@section("title")
    - Recover your account
@stop

@section("content")
    <div class="content-section__wrapper">
        <div class="content-section">

            <div id="forgot-password">

                @include("partials.feedback")

                <form action="{{ route('recover-account.post') }}" method="post">
                    @csrf

                    <forgot-password-form
                        :errors="{{ $errors->toJson() }}"
                        title-text="@lang('auth.recover_title')"
                        email-text="@lang('auth.recover_email')"
                        back-text="@lang('auth.recover_back')"
                        back-href="{{ route('login') }}"
                        submit-text="@lang('auth.recover_submit')">
                    </forgot-password-form>

                </form>

            </div>
            
        </div>
    </div>
@stop