<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Content</h3>
    </div>
    <div class="box-body">
        @yield('contenta', View::make('content_a'))
        @yield('contentb', View::make('content_b'))
        @yield('contentc', View::make('content_c'))
    </div>
</div>
