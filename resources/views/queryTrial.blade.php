<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
		<div class="wrapper">
            <div class="content-wrapper">
                <div class="contnet">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">試用表單</h3>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>公司名稱</th>
                                                <th>姓名</th>
                                                <th>email</th>
                                                <th>目的</th>
                                                <th>消息來源</th>
                                            </tr>
                                            @foreach ($trialData as $trial)
                                                <tr>
                                                    <th>{{ $trial->company_name }}</th>
                                                    <th>{{ $trial->user_name }}</th>
                                                    <th>{{ $trial->email }}</th>
                                                    {{-- <th>{{ $purposeData[$trial->purpose_id] }}</th> --}}

                                                    @if(array_key_exists($trial->purpose_id, $purposeData))
                                                        <th>{{ $purposeData[$trial->purpose_id] }}</th>
                                                    @endif

                                                    <th>
                                                        @foreach(explode(",", $trial->source) as $source)
                                                            @foreach ($sourceData as $index => $value)
                                                                @if($source == $index && $source == $ortherSourceId)
                                                                    {{ '● '. $value . ':' . $trial->other_text }}<br/>
                                                                    @break;
                                                                @elseif($source == $index)
                                                                    {{ '● '. $value }}<br/>
                                                                    @break;
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    </th>




                                                    {{-- <th>{{ $trial->source }}</th> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
		</div>
		<script src="{{mix('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
    </body>
</html>
