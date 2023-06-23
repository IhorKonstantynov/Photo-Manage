<html>
    <head>
        <title>{{ config('app.name') }} - @yield('title')</title>
    </head>
    <body>
        <div class="container" style="max-width: 600px; margin: 0 auto; background: white;">
            <div>
                <img src="{{ asset('imgs/BiggerLogo.png') }}" width="130px" style="display: block; margin: auto;" alt="Logo" />
            </div>
            <img src="{{ asset('imgs/cover_h.png') }}" alt="Cover" width="100%" />
            <div class="content" style="margin: 15px;">
                @yield('content')
            </div>
        </div>
    </body>
</html>