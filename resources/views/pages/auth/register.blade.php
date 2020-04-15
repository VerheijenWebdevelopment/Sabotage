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
                        :avatars="{{ $avatars->toJson() }}"
                        base-url="{{ asset('/') }}"
                        :strings="{{ $strings->toJson() }}"
                        login-href="{{ route('login') }}">
                    </register-form>
                
                </form>

            </div>

        </div>
    </div>
@stop