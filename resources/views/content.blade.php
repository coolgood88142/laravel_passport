<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Content</h3>
    </div>
    <div class="box-body">
        @if($presenter->checkContent($productData, 'A'))
            @include('content_a')
        @endif

        @if($presenter->checkContent($productData, 'B'))
            @include('content_b')
        @endif

        @if($presenter->checkContent($productData, 'C'))
            @include('content_c')
        @endif
    </div>
</div>
