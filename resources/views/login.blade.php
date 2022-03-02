<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <body>
        <div class="container">
            <div id="app" class="justify-content-center align-items-center">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <passport-clients></passport-clients>
                        <passport-authorized-clients></passport-authorized-clients>
                        <passport-personal-access-tokens></passport-personal-access-tokens>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="{{mix('js/app.js')}}"></script>
</html>
