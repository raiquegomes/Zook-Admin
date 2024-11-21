<div
    x-load-js="[
    @js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('swiper-script'))
    ]"
>
  <swiper-container class="mySwiper" keyboard="true" space-between="30" pagination="true" pagination-clickable="true" navigation="true">
      @foreach($images as $image)
        <swiper-slide><img src="{{ asset('storage/' . $image->path) }}" alt="Activity Image" style="width: 1080px; height: 520px; object-fit: cover;"></swiper-slide>
      @endforeach
  </swiper-container>
</div>