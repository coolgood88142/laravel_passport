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
					<div class="row">
						<div class="col-8">
							<div class="box box-info">
								<div class="box-header">
									<h3 class="box-title">選擇學員權益</h3>
								</div>
								<div class="box-body">
									<form method="post" action="{{url('/savePermission')}}">
										@csrf
											@foreach ($company_permission_group as $companyPermission)
												<div class="form-group">
													<label>{{ $product[$companyPermission->product_id] }}</label>
													@foreach ($company_permission[$companyPermission->product_id] as $data)
														<div class="col-offset-2 col-8">
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
											@endforeach
										<button type="submit" class="btn btn-primary">送出</button>
									</form>	
								</div>
							</div>
						</div>
					</div>	
				</section>														
			</div>
		</div>
		<script src="{{mix('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
    </body>
</html>
