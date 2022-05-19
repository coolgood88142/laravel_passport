<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div class="container">
            <div id="app">
                <form method="post" action="{{url('/savePermission')}}">
                    @csrf
                    @foreach ($product as $key => $value)
                        @foreach ($permission as $item)
                            <input type="checkbox" name="product[]" value="{{ $key }}" @if ($item == $key) checked @endif>{{ $value }}
                        @endforeach
                    @endforeach
                    <button type="submit">送出</button>
                </form>
            </div>
        </div>
    </body>
</html>
