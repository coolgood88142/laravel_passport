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
                                    <h3 class="box-title">產品列表</h3>
                                </div>
                                <div class="box-body">
                                    <form class="form-horizontal"  method="get" action="{{url('/queryPermissionAmount')}}">
                                        @csrf
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-3">
                                                    <input type="text" class="form-control" id="queryCompanyId" name="queryCompanyId" placeholder="公司ID" value="{{ $queryCompanyId }}">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" class="form-control" id="queryCompanyName" name="queryCompanyName" placeholder="公司名稱" value="{{ $queryCompanyName }}">
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" id="queryProduct" name="queryProduct">
                                                        <option value="">請選擇</option>
                                                        @foreach($product as $value)
                                                            <option value="{{ $value['id'] }}" @if($queryProduct == $value['id'])  selected @endif>{{ $value['name'] }}</option>
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
                                                        <th>公司ID</th>
                                                        <th>公司名稱</th>
                                                        <th>產品名稱</th>
                                                        <th>使用時段</th>
                                                        <th>已使用數量</th>
                                                        <th>剩餘數量</th>
                                                    </tr>
                                                    @foreach ($permissionAmount as $permission)
                                                        <tr>
                                                            <th>{{ $permission['company_id'] }}</th>
                                                            <th>{{ $permission['company_name'] }}</th>
                                                            <th>{{ $permission['product_name'] }}</th>
                                                            <th>{{ $permission['start_datetime'] . '-' .  $permission['end_datetime'] }}</th>
                                                            <th>{{ $permission['use_amount'] }}</th>
                                                            <th>{{ $permission['remain_amount'] }}</th>
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
</html>
