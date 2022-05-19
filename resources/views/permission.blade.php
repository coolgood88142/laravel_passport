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
                        @foreach ($permission as $index => $item)
                            <input type="checkbox" name="product[]" value="{{ $key }}" @if ($index == $key) checked @endif>{{ $value }}
                        @endforeach
                    @endforeach
                    <input type="hidden" name="permission" value="{{ json_encode($permission, true) }}">
                    <button type="submit">送出</button>
                </form>
            </div>
        </div>
    </body>
</html>
