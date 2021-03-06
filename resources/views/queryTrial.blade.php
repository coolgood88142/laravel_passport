<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
		<div class="wrapper">
            <div class="content-wrapper">
                <div class="contnet">
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">試用表單</h3>
                                </div>
                                <div class="box-body">
                                    <form class="form-horizontal"  method="get" action="{{url('/queryTrial')}}">
                                        @csrf
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-2">
                                                    <input type="text" class="form-control" id="queryCompany" name="queryCompany" placeholder="公司" value="{{ $queryCompany }}">
                                                </div>
                                                <div class="col-2">
                                                    <input type="text" class="form-control" id="queryUserName" name="queryUserName" placeholder="姓名" value="{{ $queryUserName }}">
                                                </div>
                                                <div class="col-2">
                                                    <input type="text" class="form-control" id="queryEmail" name="queryEmail" placeholder="email" value="{{ $queryEmail }}">
                                                </div>
                                                <div class="col-2">
                                                    <select class="form-control" id="queryPurpose" name="queryPurpose">
                                                        <option value="">請選擇</option>
                                                        @foreach($purposeData as $index => $value)
                                                            <option value="{{ $index }}" @if($queryPurpose == $index)  selected @endif>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-2">
                                                    <select class="js-example-basic-multiple" multiple="multiple" id="querySource" name="querySource[]" style="width: 100%;">
                                                        @foreach($sourceData as $index => $value)
                                                            <option value="{{ $index }}" @if(count($querySource) > 0 && in_array($index, $querySource))  selected @endif>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-2">
                                                    <button type="submit" class="btn btn-primary" id="query">查詢</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="form-group">
                                        <div class="row">
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
                </div>
            </div>
		</div>
		<script src="{{mix('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    </body>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
            // $('.js-example-basic-multiple').val([1,2,3]).change();
        });
    </script>
</html>
