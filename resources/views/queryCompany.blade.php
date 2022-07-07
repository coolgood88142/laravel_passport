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
                                    <h3 class="box-title">查詢公司權益</h3>
                                </div>
                                <div class="box-body">
                                    <form class="form-horizontal"  method="get" action="{{url('/sendCompanyData')}}">
                                        @csrf
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>公司名稱</label>
                                                    <select class="form-control" id="queryCompany" name="queryCompany">
                                                        <option value="">請選擇</option>
                                                        @foreach($company as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-start align-items-center">
                                            <div class="col">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary" id="sendCompany" name="sendCompany" value="Y">發送信件</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<script src="{{mix('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
    </body>
    <script>
        $(document).ready(function(){
            $('.datepicker').datepicker();

            if($('#errorMsg').val() != ''){
                document.getElementById("showModal").click();
            }
        });
    </script>
</html>
