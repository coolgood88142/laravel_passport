<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div class="container">
            <div id="app">
                <form method="post" action="{{url('/savePermission')}}">
                    @csrf
                    @foreach ($product as $value)
                        <input type="checkbox" name="product[]" value="{{ $value->id }}"
                            @foreach ($permission as $item)
                                 @if ($item->product_id == $value->id) checked @endif
                            @endforeach
                        >{{ $value->name }}
                    @endforeach
                    <button type="submit">送出</button>
                </form>
            </div>
        </div>
    </body>
</html>
