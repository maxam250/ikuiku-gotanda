@props(['shop'])

@if(!empty($shop->shop_wiget1))
<div class="shop-widget shop-widget1">
    {!! $shop->shop_wiget1 !!}
</div>
@endif
