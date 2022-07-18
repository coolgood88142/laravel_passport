<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
		<div class="wrapper">
			<div class="content-wrapper">
				<section class="content-header">
				<!--
					<h1>
						設定學員權益
						<small>Preview page</small>
					</h1>
					<ol class="breadcrumb">
					<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">設定學員權益</li>
					</ol>
				-->
				</section>
				<section class="countent">
                    @if($has_login)
					<div class="row">
						<div class="col-8">
							<div class="box box-info">
								<div class="box-header">
									<h3 class="box-title">選擇學員權益</h3>
								</div>
								<div class="box-body">
									<form method="post" action="{{url('/savePermission')}}">
										@csrf
											@foreach ($company_permission_group as $key => $companyPermission)
												<div class="form-group">
													<label>{{ $product[$companyPermission->product_id]['name'] }}</label>
													<div class="row">
                                                        <div class="col-offset-2 col-7">
                                                            @foreach ($company_permission[$companyPermission->product_id] as $data)
                                                                <div class="col">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox" class="minimal" name="product[]" value="{{ $data->id }}"
                                                                                    @foreach ($user_permission as $user)
                                                                                        @if ($data->product_id == $user->product_id &&
                                                                                                $data->start_datetime == $user->start_datetime &&
                                                                                                $data->end_datetime == $user->end_datetime)
                                                                                                checked
                                                                                        @endif
                                                                                    @endforeach
                                                                                >
                                                                        {{ '(' . $data->start_datetime . '-' . $data->end_datetime . ')' }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="col-offset-2 col-5">
                                                            <button type="submit" class="btn btn-primary" id="deleteExpiredProductId" name="deleteExpiredProductId" value="{{ $key }}">{{ '刪除過期的' . $product[$companyPermission->product_id]['name'] . '資料' }}</button>
                                                        </div>
                                                    </div>
												</div>
											@endforeach
										<button type="submit" class="btn btn-primary" >送出</button>
                                    </form>
								</div>
							</div>
						</div>
					</div>
                    @endif
                    <button type="button" class="btn btn-default" id="showModal" data-toggle="modal" data-target="#modal-default" style="display:none;">顯示視窗</button>
                    <input type="hidden" id="isShowText" value="{{ $show_text }}">

                    @if($show_text != '')
                        <div class="modal fade show" id="modal-default">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">提示訊息</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ $show_text }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        @if($has_login)
                                            {{-- <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">關閉</button> --}}
                                            <button type="button" class="btn btn btn-success" onclick="window.location.href='/mainPermission'">進入學員權益頁面</button>
                                        @else
                                            <button type="button" class="btn btn-primary pull-right" onclick="window.location.href='/login'">重新登入</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
				</section>
			</div>
		</div>
		<script src="{{mix('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
    </body>
    <script>
        if($('#isShowText').val() != ''){
            document.getElementById("showModal").click();
        }
    </script>
</html>
