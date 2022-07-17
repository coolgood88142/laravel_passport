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
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>收件人(Email)</label>
                                                    <input class="form-control" id="queryEmail" name="queryEmail">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-start align-items-center">
                                            <div class="col">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary" id="sendEmail" name="sendEmail" value="Y">發送信件</button>
                                                    <button type="submit" class="btn btn-primary" id="sendSpreadsheets" name="sendSpreadsheets" value="Y">發送google試算表</button>
                                                    <button type="button" class="btn btn-primary" id="sendAppScript" name="sendAppScript" value="Y" onclick="sendGoogleAppScript()">發送googleScript</button>
                                                    <button type="submit" class="btn btn-primary" id="sendEmailWithAttach" name="sendEmailWithAttach" value="Y">發送信件夾帶Excel檔</button>
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
        function sendGoogleAppScript(){
            let data = '';
            axios.get("/sendCompanyData?sendAppScript=Y&queryCompany=" + $('#queryCompany').val() + "&queryEmail=" + $('#queryEmail').val() ).then((response) => {
                setGoogleAppScriptSheet(response.data)
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

        function setGoogleAppScriptSheet(data){
            let companyPermissionArray = [], userPermissionArray = [], userPermissionLogArray = [], userPermissionLogGroupArray = [];
            data['companyPermission'].forEach(el => {
                companyPermissionArray.push([
                    el.company_id,
                    el.company_name,
                    el.product_id,
                    el.product_name,
                    el.amount,
                    el.date_time,
                    el.users_id,
                    el.created_at,
                    el.updated_at
                ]);
            });

            data['userPermission'].forEach(el => {
                userPermissionArray.push([
                    el.user_id,
                    el.user_name,
                    el.product_id,
                    el.product_name,
                    el.date_time,
                    el.created_at,
                    el.updated_at
                ]);
            });

            data['userPermissionLog'].forEach(el => {
                userPermissionLogArray.push([
                    el.user_id,
                    el.user_name,
                    el.product_id,
                    el.product_name,
                    el.date_time,
                    el.created_at,
                    el.updated_at
                ]);
            });

            userPermissionLogGroupArray = data['userPermissionLogGroup'];

            let params = {
                companyPermission : JSON.stringify(companyPermissionArray),
                userPermission : JSON.stringify(userPermissionArray),
                userPermissionLog : JSON.stringify(userPermissionLogArray),
                userPermissionLogGroup : JSON.stringify(userPermissionLogGroupArray),
            }
            console.log(params);
            //問題
            //如果丟一包的陣列資料進去，google app script會自動把二維陣列轉乘key，這樣在gas檔案裡無法用迴圈處理
            //解決方式
            //1.每跑一次只新增一筆資料
            //2.有半
            // var data = new URLSearchParams(new FormData(params)).toString()
            let url = 'https://script.google.com/macros/s/AKfycbyb60Js3s8SieDAVCi0yQ6FXBfgE9p6emyKxNP36NmwDqWFqr4w0WowifW8mksm5mEX/exec';
            // let config = {
            //     headers: {
            //         'Content-Type': 'multipart/form-data',
            //         'Access-Control-Allow-Origin': '*',
            //     }
            // };
            // var data = {
            //     id: '2',
            // };
            $.post(url,params,function(e){
                if(e == 'success'){
                    alert('已更新試算表')
                }
                console.log(e);
            });
            // $.post(url,data,function(e){
            //     console.log(e);
            // });
            // axios({
            //     method: 'post',
            //     url: 'https://script.google.com/macros/s/AKfycbyv8McZoskLuumK1a5rikBO9vC0MqhZ6UCvEipHFu_VhvGYAoFwRSkXYpFXVP8Wubtt/exec',
            //     data: {
            //         title: 'Fred',
            //         lastName: 'Flintstone',
            //     },
            //     headers: {
            //         'Content-Type': 'text/plain;charset=utf-8',
            //     },
            // }).then(function (response) {
            //     console.log(response);
            // }).catch(function (error) {
            //     console.log(error);
            // });
            // axios.post(url, formdata, header)
            // .then(
            //     response => (
            //         console.log('成功')
            // ))
            // .catch(error => {
            //     console.log(error.response.data);
            //     console.log(error.response.status);
            //     console.log(error.response.headers);
		    // })
            // $.ajax({
            //     type: "post",
            //     url: url,
            //     data: params,
            //     headers: header,
            //     success: function(response) {
            //         console.log('成功')
            //     }
            // });
        }
    </script>
</html>
