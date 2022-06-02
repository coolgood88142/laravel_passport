<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Header</h3>
    </div>
    <div class="box-body">
        @extends('main_permission')

        @section('header')

            <input type="button" value="A">

            <input type="button" value="B">

            <input type="button" value="C">

        @show
        {{-- @inject('presenter', 'App\Presenters\UserPermissionPresenter')
        @if($presenter->checkHeader($presenter->matchProductId($permission, $product), 'A'))
            <input type="button" value="A">
        @endif

        @if($presenter->checkHeader($presenter->matchProductId($permission, $product), 'B'))
            <input type="button" value="B">
        @endif

        @if($presenter->checkHeader($presenter->matchProductId($permission, $product), 'C'))
            <input type="button" value="C">
        @endif --}}
    </div>
</div>
