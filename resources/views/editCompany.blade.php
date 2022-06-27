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
                                    <h3 class="box-title">編輯公司資料</h3>
                                </div>
                                <div class="box-body">
                                    <form class="form-horizontal"  method="get" action="{{url('/editCompany')}}">
                                        @csrf
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>公司名稱</label>
                                                    <select class="form-control" id="queryCompany" name="queryCompany">
                                                        <option value="">請選擇</option>
                                                        @foreach($company as $value)
                                                            <option value="{{ $value->id }}" @if($companyId == $value->id)  selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-start align-items-center">
                                            <div class="col">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary" id="queryCompanyPermission" name="queryCompanyPermission" value="Y">查詢</button>
                                                </div>
                                            </div>
                                        </div>
                                        @if($companyId != '')
                                            <input type="hidden" class="form-control" id="companyId" name="companyId" placeholder="公司ID" value="{{ $companyId }}">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>選擇產品</label>
                                                        <select class="form-control" id="productId" name="productId">
                                                            <option value="">請選擇</option>
                                                            @foreach($product as $value)
                                                                <option value="{{ $value->id }}" @if($productId == $value->id)  selected @endif>{{ $value->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>產品數量</label>
                                                        <input type="number" class="form-control" id="amount" name="amount" placeholder="數量" value="{{ $amount }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>使用時段-起始日期</label>
                                                        <input type="text" class="form-control pull-right" id="startDateTime" name="startDateTime" data-provide="datepicker" data-date-format="yyyy-mm-dd 00:00:00" value="{{ $startDateTime }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>使用時段-截止日期</label>
                                                        <input type="text" class="form-control pull-right" id="endDateTime" name="endDateTime" data-provide="datepicker" data-date-format="yyyy-mm-dd 00:00:00" value="{{ $endDateTime }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-start align-items-center">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary" id="addCompanyPermission" name="addCompanyPermission" value="Y">新增</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <button type="button" class="btn btn-default" id="showModal" data-toggle="modal" data-target="#modal-default" style="display:none;">顯示視窗</button>
                                        <input type="hidden" id="errorMsg" value="{{ $errorMsg }}">

                                        @if($errorMsg != '')
                                            <div class="modal fade show" id="modal-default">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">提示訊息</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ $errorMsg }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">關閉</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
