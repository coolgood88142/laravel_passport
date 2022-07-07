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
                    <th>產品ID</th>
                    <th>數量</th>
                    <th>使用時段-起始時間</th>
                    <th>使用時段-截止時間</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                </tr>
                @foreach($companyPermission as $company)
                <tr>
                    <th>{{ $company->company_id }}</th>
                    <th>{{ $company->product_id }}</th>
                    <th>{{ $company->amount }}</th>
                    <th>{{ $company->start_datetime }}</th>
                    <th>{{ $company->end_datetime }}</th>
                    <th>{{ $company->created_at }}</th>
                    <th>{{ $company->updated_at }}</th>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br/>

        <h3>產品</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>產品名稱</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                </tr>
                @foreach($productData as $data)
                <tr>
                    <th>{{ $data->name }}</th>
                    <th>{{ $data->created_at }}</th>
                    <th>{{ $data->updated_at }}</th>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br/>

        <h3>學員權益</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>學員ID</th>
                    <th>產品ID</th>
                    <th>使用時段-起始時間</th>
                    <th>使用時段-截止時間</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                </tr>
                @foreach($userPermission as $user)
                <tr>
                    <th>{{ $user->user_id }}</th>
                    <th>{{ $user->product_id }}</th>
                    <th>{{ $user->start_datetime }}</th>
                    <th>{{ $user->end_datetime }}</th>
                    <th>{{ $user->created_at }}</th>
                    <th>{{ $user->updated_at }}</th>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br/>
    </body>
</html>
