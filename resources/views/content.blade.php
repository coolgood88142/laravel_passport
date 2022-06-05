<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Content</h3>
    </div>
    <div class="box-body">
        @if($presenter->checkContent($productData, 'A'))
            @include('contentA')
        @endif

        @if($presenter->checkContent($productData, 'B'))
            @include('contentB')
        @endif

        @if($presenter->checkContent($productData, 'C'))
            @include('contentC')
        @endif
    </div>
</div>
