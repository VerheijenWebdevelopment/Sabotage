@extends("layouts.auth")

@section("title")
    - Recover your account
@stop

@section("content")
    <div class="content-section__wrapper">
        <div class="content-section">

            <div id="recover-mail-sent" class="elevation-1">
                <h1 id="recover-mail-sent__title">@lang("auth.recover_email_sent_title")</h1>
                <div id="recover-mail-sent__text">@lang("auth.recover_email_sent_text")</div>
                <div id="recover-mail-sent__actions">
                    <v-btn text color="primary" href="{{ route('login') }}">
                        <i class="fas fa-arrow-left"></i>
                        @lang("auth.recover_email_sent_back")
                    </v-btn>
                </div>
            </div>
            
        </div>
    </div>
@stop