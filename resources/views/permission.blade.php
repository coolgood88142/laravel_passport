<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div class="container">
            <div id="app">
                <form method="post" action="{{url('/savePermission')}}">
                    @csrf
                    {{-- @foreach ($product as $value)
                        <input type="checkbox" name="product[]" value="{{ $value->id }}"
                            @foreach ($permission as $item)
                                 @if ($item->product_id == $value->id) checked @endif
                            @endforeach
                        >{{ $value->name }}
                    @endforeach --}}

                    {{-- @foreach ($user_permission as $user)
                        <input type="checkbox" name="product[]" value="{{ $user->product_id }}"
                        @foreach ($company_permission as $company)
                            @foreach ($company as $data)
                                @if ($data->product_id == $user->product_id &&
                                    $data->start_datetime == $user->start_datetime &&
                                    $data->end_datetime == $user->end_datetime)
                                    checked
                                @endif
                            @endforeach
                        @endforeach
                        >{{ $product[intval($user->product_id)] }}
                    @endforeach --}}

                    @foreach ($company_permission_group as $companyPermission)
                        <fieldset>
                            <legend>{{ $product[$companyPermission->product_id] }}</legend>
                            @foreach ($company_permission[$companyPermission->product_id] as $data)
                                <div>
                                    <input type="checkbox" name="product[]" value="{{ $data->id }}"
                                    @foreach ($user_permission as $user)
                                        @if ($data->product_id == $user->product_id &&
                                            $data->start_datetime == $user->start_datetime &&
                                            $data->end_datetime == $user->end_datetime)
                                            checked
                                        @endif
                                    @endforeach
                                    >{{ '(' . $data->start_datetime . '-' . $data->end_datetime . ')' }}
                                </div>
                            @endforeach

                        </fieldset>
                    @endforeach
                    <br/>

                    {{-- @foreach ($company_permission as $company)
                        @foreach ($company as $index => $data)
                        <input type="checkbox" name="product[]" value="{{ $data->id }}"
                            @foreach ($user_permission as $user)
                                @if ($data->product_id == $user->product_id &&
                                    $data->start_datetime == $user->start_datetime &&
                                    $data->end_datetime == $user->end_datetime)
                                    checked
                                @endif
                            @endforeach
                        >{{ $product[$data["product_id"]] }}
                        @endforeach
                    @endforeach --}}
                    <button type="submit">送出</button>
                </form>
            </div>
        </div>
    </body>
</html>
