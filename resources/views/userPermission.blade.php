<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
		<div class="wrapper">
            <div class="content-wrapper">
                <div  id="app" class="contnet">
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">學員權益明細表</h3>
                                </div>
                                <div class="box-body">
                                    <form class="form-horizontal"  method="get" action="{{url('/getUserPermissionDeatils')}}">
                                        @csrf
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-10">
                                                    <input type="text" class="form-control" id="queryUserId" name="queryUserId" placeholder="學員ID" value="{{ $queryUserId }}">
                                                </div>
                                                <div class="col-2">
                                                    <div class="row justify-content-center">
                                                        <button type="submit" class="btn btn-primary" id="query">查詢</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="form-group">
                                        <div class="row">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th>學員ID</th>
                                                        <th>學員名稱</th>
                                                        <th>權益名稱</th>
                                                        <th>使用時段</th>
                                                        <th>新增時間</th>
                                                    </tr>
                                                    @foreach ($userPermission as $permission)
                                                        <tr>
                                                            <th>{{ $permission['user_id'] }}</th>
                                                            <th>{{ $permission['user_name'] }}</th>
                                                            <th>{{ $permission['product_name'] }}</th>
                                                            <th>{{ $permission['start_datetime'] . '-' .  $permission['end_datetime'] }}</th>
                                                            <th>{{ $permission['created_at'] }}</th>
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
    </body>
</html>
