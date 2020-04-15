<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Saboteur @yield("title")</title>
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
        
        {{-- Laravel Mix - CSS File --}}
        <link rel="stylesheet" href="{{ mix('css/lobby.css') }}">

        {{-- Font Awesome --}}
        <script defer src="https://use.fontawesome.com/releases/v5.8.2/js/all.js" integrity="sha384-DJ25uNYET2XCl5ZF++U8eNxPWqcKohUUBUpKGlNLMchM7q4Wjg2CUpjHLaL8yYPH" crossorigin="anonymous"></script>
        
    </head>
    <body>

        <v-app id="app" class="dark">
            
            <!-- Topnav -->
            <div id="topnav-wrapper">
                <div id="topnav">
                    <div id="topnav-logo">
                        <a href="{{ route('lobby') }}">
                            <div id="topnav-logo__text">
                                Saboteur
                            </div>
                        </a>
                    </div>
                    <ul id="topnav-links">
                        <li>
                            <a href="{{ route('lobby') }}">@lang("lobby.layout_lobby")</a>
                        </li>
                        <li>
                            <a href="{{ route('lobby.leaderboards') }}">@lang("lobby.layout_highscores")</a>
                        </li>
                        @if (auth()->user()->is_admin)
                            <li>
                                <a href="{{ route('horizon.index') }}">Horizon</a>
                            </li>
                            <li>
                                <a href="{{ url('/laravel-websockets') }}">Websockets</a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('settings') }}">@lang("lobby.layout_settings")</a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}">@lang("lobby.layout_logout")</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Content -->
            <div id="content-wrapper">
                @yield("content")
            </div>

            <!-- Footer -->
            <div id="footer-wrapper">
                <footer id="footer">
                    &copy; OpperKabouter Inc. 2020 - &infin;
                </footer>
            </div>

        </v-app>

        {{-- Laravel Mix - JS File --}}
        <script src="{{ mix('js/app.js') }}"></script>

    </body>
</html>