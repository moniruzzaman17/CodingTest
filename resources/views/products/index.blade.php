@extends('layouts.app')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Products</h1>
</div>


<div class="card">
    <form action="{{route('product.filter')}}" method="get" class="card-header">
        <div class="form-row justify-content-between">
            <div class="col-md-2">
                <input type="text" name="title" placeholder="Product Title" class="form-control" value="{{old('title')}}">
            </div>
            <div class="col-md-2">
                <select name="variant" id="" class="form-control">
                    <option value="">Select Variant</option>
                    @foreach ($productVariants as $key => $productVariant)
                    <optgroup label="{{ucwords($productVariant->title)}}">
                    @foreach ($productVariant->variantTag as $key => $variantTag)

                    <option value="{{$variantTag->variant}}">{{$variantTag->variant}}</option>
                    @endforeach
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Price Range</span>
                    </div>
                    <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                    <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                </div>
            </div>
            <div class="col-md-2">
                <input type="date" name="date" placeholder="Date" class="form-control">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
    <div class="card-body">
        <div class="table-response">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th width="30%">Description</th>
                        <th>Variant</th>
                        <th width="50px">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($products as $key => $product)
                    <tr>
                        <td>{{$product->id}}</td>
                        <td>{{$product->title}} <br> Created at : {{$product->created_at->format('jS F Y h:i:s A')}}</td>
                        <td>{!!$product->description!!}</td>
                        <td>
                            {{-- checking is product has variant or not --}}
                            @if(count($product->variantPrice)>1)
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant{{$product->id}}">
                                @foreach ($product->variantPrice as $key => $variantPrice)
                                <dt class="col-sm-3 pb-0">
                                    {{$variantPrice->variantOne->variant}} {{!empty($variantPrice->product_variant_two) ? '/'.$variantPrice->variantTwo->variant : ''}} {{!empty($variantPrice->product_variant_three) ? '/'.$variantPrice->variantThree->variant : ''}}
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($variantPrice->price,2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format($variantPrice->stock,0) }}</dd>
                                    </dl>
                                </dd>
                                @endforeach
                            </dl>
                            <button onclick="$('#variant{{$product->id}}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            @else
                            <dl class="row mb-0" style="height: 80px; overflow: hidden">
                                <dt class="col-sm-3 pb-0">
                                    N/A
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($product->variantPrice[0]->price,2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format($product->variantPrice[0]->stock,0) }}</dd>
                                    </dl>
                                </dd>
                            </dl>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit',$product->id) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>

    <div class="card-footer">
        <div class="row justify-content-between">
            <div class="col-md-6">
                <p>Showing {{$products->firstItem()}} to {{$products->lastItem()}} out of {{$products->total()}}</p>
            </div>
            <div class="col-md-2">

            </div>
        </div>
    </div>
    <div class="product-pagination d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>

@endsection
