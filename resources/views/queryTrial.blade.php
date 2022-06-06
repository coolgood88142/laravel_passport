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
                                                    <th>{{ $trial['company_name'] }}</th>
                                                    <th>{{ $trial['user_name'] }}</th>
                                                    <th>{{ $trial['email'] }}</th>
                                                    <th>{{ $trial['purpose_text'] }}</th>
                                                    <th>
                                                        <ul>
                                                            @foreach ($trial['source_text'] as $text)
                                                                <li>
                                                                    {{ $text }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </th>
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
