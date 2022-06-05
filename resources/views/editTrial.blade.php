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
                                        <div class="form-group @error('inputCompany') ? 'has-error' : '' @enderror">
                                            <label for="inputCompany" class="col-sm-2 control-label">公司</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="inputCompany" id="inputCompany" value="{{ old('inputCompany') }}">
                                                <span class="help-block" style="@error('inputCompany') ? '' : 'display:none;' @enderror">請輸入公司名稱</span>
                                            </div>
                                        </div>
                                        <div class="form-group @error('inputUserName') ? 'has-error' : '' @enderror">
                                            <label for="inputUserName" class="col-sm-2 control-label">姓名</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="inputUserName" id="inputUserName" value="{{ old('inputUserName') }}">
                                                <span class="help-block" style="@error('inputUserName') ? '' : 'display:none;' @enderror">請輸入姓名</span>
                                            </div>
                                        </div>
                                        <div class="form-group @error('inputEmail') ? 'has-error' : '' @enderror">
                                            <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" name="inputEmail" id="inputEmail" value="{{ old('inputEmail') }}">
                                                <span class="help-block" style="@error('inputEmail') ? '' : 'display:none;' @enderror">請輸入email</span>
                                            </div>
                                        </div>
                                        <div class="form-group @error('purposeRadios') ? 'has-error' : '' @enderror">
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
                                            <div class="col-sm-10">
                                                <span class="help-block"  style="@error('purposeRadios') ? '' : 'display:none;' @enderror">至少選擇一個目的</span>
                                            </div>
                                        </div>
                                        <div class="form-group @error('sourceCheckBoxs') ? 'has-error' : '' @enderror">
                                            <label for="sourceCheckBoxs" class="col-sm-2 control-label">消息來源</label>
                                            <div class="col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="sourceCheckBoxs[]" id="sourceCheckBoxs1" value="1" @if(old('sourceCheckBoxs') != null && in_array(1, old('sourceCheckBoxs'))) checked @endif>
                                                            fb
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="sourceCheckBoxs[]" id="sourceCheckBoxs2" value="2" @if(old('sourceCheckBoxs') != null && in_array(2, old('sourceCheckBoxs'))) checked @endif>
                                                            google
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="sourceCheckBoxs[]" id="sourceCheckBoxs3" value="3" @if(old('sourceCheckBoxs') != null && in_array(3, old('sourceCheckBoxs'))) checked @endif>
                                                            朋友介紹
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="sourceCheckBoxs[]" id="sourceCheckBoxs4" value="4" @if(old('sourceCheckBoxs') != null && in_array(4, old('sourceCheckBoxs'))) checked @endif>
                                                            其他
                                                        <input type="text" name="otherText" id="otherText" value="{{ old('sourceCheckBoxs') != null && in_array(4, old('sourceCheckBoxs')) ? $otherText : '' }}" disabled >
                                                        <div class="form-group @error('otherText')  ? 'has-error' : '' @enderror">
                                                            <span class="help-block"  style="@error('otherText') ? '' : 'display:none;' @enderror">請輸入其他原因</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-10">
                                                <span class="help-block"  style="@error('sourceCheckBoxs') ? '' : 'display:none;' @enderror">至少勾選一個消息來源</span>
                                            </div>
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
        $(document).ready(function() {
            // if($('#errorText').val() != ''){
            //     alert($('#errorText').val());
            // }
        });

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
