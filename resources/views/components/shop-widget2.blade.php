@props(['shop'])

@if(!empty($shop->shop_wiget2))
<div class="shop-widget shop-widget2">
    {!! $shop->shop_wiget2 !!}
</div>
@endif
