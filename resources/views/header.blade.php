<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Header</h3>
    </div>
    <div class="box-body">
        @if($presenter->checkHeader($productData))
            <input type="button" value="A">
        @endif

        @if($presenter->checkHeader($productData))
            <input type="button" value="B">
        @endif

        @if($presenter->checkHeader($productData))
            <input type="button" value="C">
        @endif
    </div>
</div>
