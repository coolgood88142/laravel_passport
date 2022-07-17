<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <h3>公司權益</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>公司ID</th>
                    <th>公司名稱</th>
                    <th>產品ID</th>
                    <th>產品名稱</th>
                    <th>數量</th>
                    <th>使用時段</th>
                    <th>學員ID</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                </tr>
                @if(count($companyPermission) > 0)
                    @foreach($companyPermission as $company)
                    <tr>
                        <th>{{ $company['company_id'] }}</th>
                        <th>{{ $company['company_name']}}</th>
                        <th>{{ $company['product_id'] }}</th>
                        <th>{{ $company['product_name'] }}</th>
                        <th>{{ $company['amount'] }}</th>
                        <th>{{ $company['date_time'] }}</th>
                        <th>{{ $company['users_id'] }}</th>
                        <th>{{ $company['created_at'] }}</th>
                        <th>{{ $company['updated_at'] }}</th>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <h4>無資料!</h4>
                    </tr>
                @endif
            </tbody>
        </table>
        <br/>

        <h3>學員權益</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>學員ID</th>
                    <th>學員姓名</th>
                    <th>產品ID</th>
                    <th>產品名稱</th>
                    <th>使用時段</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                </tr>
                @if(count($userPermission) > 0)
                    @foreach($userPermission as $user)
                    <tr>
                        <th>{{ $user['user_id'] }}</th>
                        <th>{{ $user['user_name'] }}</th>
                        <th>{{ $user['product_id'] }}</th>
                        <th>{{ $user['product_name'] }}</th>
                        <th>{{ $user['date_time'] }}</th>
                        <th>{{ $user['created_at'] }}</th>
                        <th>{{ $user['updated_at'] }}</th>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <h4>無資料!</h4>
                    </tr>
                @endif
            </tbody>
        </table>
        <br/>

        <h3>學員權益變更記錄</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>學員ID</th>
                    <th>學員姓名</th>
                    <th>產品ID</th>
                    <th>產品名稱</th>
                    <th>使用時段</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                </tr>
                @if(count($userPermissionLog) > 0)
                    @foreach($userPermissionLog as $user)
                    <tr>
                        <th>{{ $user['user_id'] }}</th>
                        <th>{{ $user['user_name'] }}</th>
                        <th>{{ $user['product_id'] }}</th>
                        <th>{{ $user['product_name'] }}</th>
                        <th>{{ $user['date_time'] }}</th>
                        <th>{{ $user['created_at'] }}</th>
                        <th>{{ $user['updated_at'] }}</th>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <h4>無資料!</h4>
                    </tr>
                @endif
            </tbody>
        </table>
        <br/>
    </body>
</html>
