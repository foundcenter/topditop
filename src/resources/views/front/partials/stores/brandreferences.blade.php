@if(count($brandreferences))
<section class="brandreferences-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-heading">Marken Referenzen</h3>
                <div class="brandreferences-macy">
                    @foreach($brandreferences as $brandreference)
                        <div class="brandreference">
                            <a href="{{ route('front_brand_stores', $brandreference->manufacturer_id) }}">
                                <img src="{{$brandreference->getThumbnailMediumUrl()}}">
                            </a>
                            <div class="brandreference-text">
                                <p class="brandreference-text-title">{{ $brandreference->manufacturer->name }}: {{$brandreference->title}}
                                    @if($brandreference->category != null)
                                        <span class="brandreference-text-category">{{$brandreference->category->name}}</span>
                                    @endif
                                </p>
                                <p class="brandreference-text-description">{{$brandreference->description}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif