<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div class="container">
            <div id="app">
                {{ $showText }}
                <button type="button" onclick="window.location.href='/permission'">回選擇頁</button>
            </div>
        </div>
    </body>
</html>
