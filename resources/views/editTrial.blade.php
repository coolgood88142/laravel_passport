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
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">試用表單</h3>
                                </div>
                                <form class="form-horizontal"  method="post" action="{{url('/saveTrial')}}">
                                    @csrf
                                    <div class="box-body">
                                        <div class="form-group @error('inputCompany') has-error @enderror">
                                            <label for="inputCompany" class="col-sm-2 control-label">公司</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="inputCompany" id="inputCompany" value="{{ old('inputCompany') }}">
                                                @error('inputCompany')
                                                    <span class="help-block">{{ $errors->first('inputCompany') }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group @error('inputUserName') has-error @enderror">
                                            <label for="inputUserName" class="col-sm-2 control-label">姓名</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="inputUserName" id="inputUserName" value="{{ old('inputUserName') }}">
                                                @error('inputUserName')
                                                    <span class="help-block">{{ $errors->first('inputUserName') }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group @error('inputEmail') has-error @enderror">
                                            <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" name="inputEmail" id="inputEmail" value="{{ old('inputEmail') }}">
                                                @error('inputEmail')
                                                    <span class="help-block">{{ $errors->first('inputEmail') }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group @error('purposeRadios') has-error @enderror">
                                            <label for="purposeRadios" class="col-sm-2 control-label">目的</label>
                                            <div class="col-sm-10">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="purposeRadios" id="purposeRadios1" value="1" @if( old('purposeRadios') == 1) checked @endif>
                                                            公司使用
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="purposeRadios" id="purposeRadios2" value="2" @if( old('purposeRadios') == 2) checked @endif>
                                                            個人使用
                                                    </label>
                                                </div>
                                            </div>
                                            @error('purposeRadios')
                                                <div class="col-sm-10">
                                                    <span class="help-block">{{ $errors->first('purposeRadios') }}</span>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group @error('sourceCheckBoxs') has-error @enderror">
                                            <label for="sourceCheckBoxs" class="col-sm-2 control-label">消息來源</label>
                                            @foreach($sourceData as $index => $value)
                                                <div class="col-sm-10">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="sourceCheckBoxs[]" id="sourceCheckBoxs{{ $index }}" value="{{ $index }}" @if(is_array(old('sourceCheckBoxs')) && in_array($index, old('sourceCheckBoxs'))) checked @endif>
                                                                {{ $value }}
                                                                @if($ortherSourceId == $index)
                                                                    <input type="text" name="otherText" id="otherText" value="{{ old('otherText') }}" @if(!is_array(old('sourceCheckBoxs')) || !in_array($ortherSourceId, old('sourceCheckBoxs'))) disabled @endif>
                                                                    <div class="form-group @error('otherText') has-error @enderror">
                                                                        @error('otherText')
                                                                            <span class="help-block">{{ $errors->first('otherText') }}</span>
                                                                        @enderror
                                                                    </div>
                                                                @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @error('sourceCheckBoxs')
                                                <div class="col-sm-10">
                                                    <span class="help-block">{{ $errors->first('sourceCheckBoxs') }}</span>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="agreePrivacy" id="agreePrivacy" value="Y">
                                                        同意隱私條款
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary" id="send" disabled>送出</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
            </div>
		</div>
		<script src="{{mix('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
    </body>
    <script>
        $('#sourceCheckBoxs4').click(function(){
            if($(this).is(':checked')){
                $('#otherText').attr('disabled', false);
            }else{
                $('#otherText').attr('disabled', true);
            }
        });

        $('#agreePrivacy').click(function(){
            if($(this).is(':checked')){
                $('#send').attr('disabled', false);
            }else{
                $('#send').attr('disabled', true);
            }
        });
    </script>
</html>
