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
                                                <div class="col-2">
                                                    <select class="form-control" id="queryProduct" name="queryProduct">
                                                        <option value="">請選擇</option>
                                                        @foreach($product as $value)
                                                            <option value="{{ $value['id'] }}" @if($queryProduct == $value['id'])  selected @endif>{{ $value['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-1">
                                                    <div class="row justify-content-center">
                                                        <button type="submit" class="btn btn-primary" id="query">查詢</button>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="row">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="Y" id="queryOrdData" name="queryOrdData" @if($queryOrdData == 'Y')  checked @endif>
                                                            <label class="form-check-label" for="queryOrdData">
                                                              過期資料
                                                            </label>
                                                          </div>
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
                                                            <th>
                                                                <div class="row justify-content-center">
                                                                    {{ $permission['use_amount'] }}
                                                                </div>
                                                                <div class="row justify-content-center">
                                                                    <input type="button" class="btn btn-primary" value="明細" onclick="openUserPermissionModal({{$permission['company_id']}}, {{$permission['product_id']}}, {{$permission['start_datetime']}}, {{$permission['end_datetime']}})">
                                                                </div>
                                                            </th>
                                                            <th>{{ $permission['remain_amount'] }}</th>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="modal-default">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"></h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span></button>
                                                </div>
                                                <div class="modal-body">

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">關閉</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="modalCompanyId" value="">
                                    <input type="hidden" id="modalProductId" value="">
                                    <input type="hidden" id="modalStartDatetime" value="">
                                    <input type="hidden" id="modalEndDatetime" value="">
                                    <input type="hidden" id="modalUserName" value="">
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
        function openUserPermissionModal(company_id, product_id, start_datetime, end_datetime, user_name){
            $('#modalCompanyId').val(company_id);
            $('#modalProductId').val(product_id);
            $('#modalStartDatetime').val(start_datetime);
            $('#modalEndDatetime').val(end_datetime);
            $('#modalUserName').val(user_name);

            // $()
            $('#modal-default').modal('show');
        }

        function openUserPermissionModal(company_id, product_id, start_datetime, end_datetime){
            axios.get("/getUserPermission?company_id="+company_id +"&product_id=" + product_id + "&start_datetime=" + start_datetime +   "&end_datetime=" + end_datetime + "&page=" + page).then((response) => {
				this.data = response.data
                let table = '';

                foreach(this.data as){

                }

                console.log(this.data);
			}).catch((error) => {
				if (error.response) {
					console.log(error.response.data)
					console.log(error.response.status)
					console.log(error.response.headers)
				} else {
					console.log("Error", error.message)
				}
			})
        }
    </script>
</html>
