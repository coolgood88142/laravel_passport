<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
		<div class="wrapper">
            <div class="content-wrapper">
                <div class="contnet">
                    <div class="row">
                        <div class="col-8">
                            @inject('presenter', 'App\Presenters\UserPermissionPresenter')
                            @includeWhen(count($presenter->matchProductId($permission, $product)) == 0 ,
                            'noPermission', [])

                            @includeWhen(count($presenter->matchProductId($permission, $product)) > 0 ,
                             'header',
                             ['productData' => $presenter->matchProductId($permission, $product), 'presenter' => $presenter])

                            @includeWhen(count($presenter->matchProductId($permission, $product)) > 0 ,
                            'content',
                            ['productData' => $presenter->matchProductId($permission, $product), 'presenter' => $presenter])

                            @includeWhen($presenter->checkFooter($presenter->matchProductId($permission, $product)) ,
                            'footer',
                            ['showFooter' => true])
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <button type="submit" class="btn btn-primary" onclick="window.location.href='/permission'">回權益選擇頁</button>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<script src="{{mix('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
    </body>
</html>
