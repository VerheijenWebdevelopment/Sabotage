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
        <link rel="stylesheet" href="{{ mix('css/auth.css') }}">

        {{-- Font Awesome --}}
        <script defer src="https://use.fontawesome.com/releases/v5.8.2/js/all.js" integrity="sha384-DJ25uNYET2XCl5ZF++U8eNxPWqcKohUUBUpKGlNLMchM7q4Wjg2CUpjHLaL8yYPH" crossorigin="anonymous"></script>
        
    </head>
    <body>

        <v-app id="app" class="dark">
            
            <!-- Content -->
            <div id="content-wrapper">
                
                <!-- Logo -->
                <a id="logo" href="{{ route('login') }}">
                    <div id="logo-text">Saboteur</div>
                    <h2 id="page-subtitle">Ben jij een sneaky kaboutertje?</h2>
                </a>

                <!-- Page content -->
                @yield("content")

            </div>
            
            <!-- Footer -->
            <div id="footer-wrapper">
                <footer id="footer">
                    &copy; Copyrighted by.. someone else probably.
                </footer>
            </div>

        </v-app>

        {{-- Laravel Mix - JS File --}}
        <script src="{{ mix('js/app.js') }}"></script>

    </body>
</html>