@extends("layouts.lobby")

@section("title")
    - Settings
@stop

@section("content")

    <!-- Header -->
    <div id="page-header">
        <h1 id="page-header__title">
            @lang("settings.overview_title")
        </h1>
    </div>

    <!-- Feedback -->
    @include("partials.feedback")

    <!-- Settings overview -->
    <div id="settings">

        <!-- Profile -->
        <div class="setting-wrapper">
            <a class="setting elevation-1" href="{{ route('settings.profile') }}">
                <span class="setting-icon">
                    <i class="fas fa-user"></i>
                </span>
                <span class="setting-text">
                    @lang("settings.overview_my_account")
                </span>
            </a>
        </div>

        <!-- Update profile -->
        <div class="setting-wrapper">
            <a class="setting elevation-1" href="{{ route('settings.update-profile') }}">
                <span class="setting-icon">
                    <i class="fas fa-user-edit"></i>
                </span>
                <span class="setting-text">
                    @lang("settings.overview_update_account")
                </span>
            </a>
        </div>

        <!-- Change password -->
        <div class="setting-wrapper">
            <a class="setting elevation-1" href="{{ route('settings.change-password') }}">
                <span class="setting-icon">
                    <i class="fas fa-key"></i>
                </span>
                <span class="setting-text">
                    @lang("settings.overview_change_password")
                </span>
            </a>
        </div>

    </div>

@stop