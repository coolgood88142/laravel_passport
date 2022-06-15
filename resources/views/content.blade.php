<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Content</h3>
    </div>
    <div class="box-body">
        @if($presenter->checkContentA($productData))
            @include('content_a')
        @endif

        @if($presenter->checkContentB($productData))
            @include('content_b')
        @endif

        @if($presenter->checkContentC($productData))
            @include('content_c')
        @endif
    </div>
</div>
