<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('price','variantPrice','variantPrice.variantOne','variantPrice.variantTwo','variantPrice.variantThree')->orderBy('id','Desc')->paginate(2)->onEachSide(0);
        // dd($products[0]->variantPrice[0]->variantOne->variant);

        $productVariants = Variant::with('variantTag')->get();

        // $productVariants = ProductVariant::with('variants')->distinct('variant')->get()->groupBy('variant');

        // $productVariants = ProductVariant::join('variants','variants.id','product_variants.variant_id')->select('product_variants.variant','variants.title')->get()->groupBy('variants.title');
        // dd($productVariants);
        return view('products.index', compact('products','productVariants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // return response()->json($data['productIDforEdit']);
        if ($data['createProduct'] == true) {

        // $image = $data['product_image'][0]['files'];

        // $image->move(public_path('../../public_html/p'));
        // return response()->json($data['product_image']['']);

            $products = new Product;

            $products->title        = $data['title'];
            $products->sku          = $data['sku'];
            $products->description  = $data['description'];
            $products->save();
            $productID = $products->id;

            foreach ($data['product_variant'] as $key => $productVariants) {
                // return response()->json($productVariants['option']);
            // return response()->json($productVariants['tags']);
                foreach ($productVariants['tags'] as $key => $tag) {
                    $product_variants           = new ProductVariant;
                    $product_variants->variant  = $tag;
                    $product_variants->variant_id  = $productVariants['option'];
                    $product_variants->product_id  = $productID;
                    $product_variants->save();
                }
            }
            foreach ($data['product_variant_prices'] as $key => $variantPrices) {
                $productPrice           = new ProductVariantPrice;

                $titles = explode("/",$variantPrices['title']);
                if (!empty($titles[0])) {
                    $variationOne = ProductVariant::where('product_id',$productID)->where('variant',$titles[0])->first()->id;
                    $productPrice->product_variant_one    = $variationOne;
                }
                if (!empty($titles[1])) {
                    $variationTwo = ProductVariant::where('product_id',$productID)->where('variant',$titles[1])->first()->id;
                    $productPrice->product_variant_two    = $variationTwo;
                }

                if (!empty($titles[2])) {
                    $variationThree = ProductVariant::where('product_id',$productID)->where('variant',$titles[2])->first()->id;
                    $productPrice->product_variant_three    = $variationThree;
                }
                $productPrice->price        = $variantPrices['price'];
                $productPrice->stock        = $variantPrices['stock'];
                $productPrice->product_id   = $productID;

                $productPrice->save();
            }

            for ($i=0; $i < count($data['product_image']); $i++){
                $productimages = new ProductImage;
                $productimages->product_id = $productID;
                $productimages->file_path = $data['product_image'][$i]['filename'];
                $productimages->save();
            }

            return response()->json('success');
        }
        else{
            // product edit
            $products = Product::where('id',$data['productIDforEdit'])->update([
                'title'    => $data['title'],
                'description'    => $data['description']
            ]);
            $productID = $data['productIDforEdit'];

            foreach ($data['product_variant_prices'] as $key => $variantPrices) {

                $titles = explode("/",$variantPrices['title']);

                if (!empty($titles[0])) {
                    $variationOne = ProductVariant::where('product_id',$productID)->where('variant',$titles[0])->first()->id;
                    // $productPrice->product_variant_one    = $variationOne;
                }
                else{
                    $variationOne = null;
                }
                if (!empty($titles[1])) {
                    $variationTwo = ProductVariant::where('product_id',$productID)->where('variant',$titles[1])->first()->id;
                    // $productPrice->product_variant_two    = $variationTwo;
                }
                else{
                    $variationTwo = null;
                }

                if (!empty($titles[2])) {
                    $variationThree = ProductVariant::where('product_id',$productID)->where('variant',$titles[2])->first()->id;
                    // $productPrice->product_variant_three    = $variationThree;
                }
                else{
                    $variationThree = null;
                }

                $productPrice           = ProductVariantPrice::where('product_id',$productID)->where('product_variant_one',$variationOne)->where('product_variant_two',$variationTwo)->where('product_variant_three',$variationThree)->update([
                    'price'        => $variantPrices['price'],
                    'stock'        => $variantPrices['stock']
                ]);
            }

            // for ($i=0; $i < count($data['product_image']); $i++){
            //     $productimages = new ProductImage;
            //     $productimages->product_id = $productID;
            //     $productimages->file_path = $data['product_image'][$i]['filename'];
            //     $productimages->save();
            // }

            return response()->json('success');
        }



    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, Request $request)
    {
        $productID = request('product')->id;
        $variants = Variant::all();
        $products = Product::with('price','variantPrice','images','variantPrice.variantOne','variantPrice.variantTwo','variantPrice.variantThree')->where('id',$productID)->first();
// dd($products);
        return view('products.edit', compact('variants','products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    /**
     * Filter.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        // dd($request->all());
        $title      = !empty(request('title'))?request('title'):null;
        $variant    = !empty(request('variant'))?request('variant'):null;
        $price_from = !empty(request('price_from'))?request('price_from'):0;
        $price_to   = !empty(request('price_to'))?request('price_to'):0;
        $date       = !empty(request('date'))?request('date'):null;

        $products    = Product::with([
            'variantPrice'=> function($priceQuery) use ($price_from,$price_to) {
                $priceQuery->where('price', '<', $price_from)
                ->orWhere('price', '>', $price_to);
            }
            ,'variantPrice.variantOne','variantPrice.variantTwo','variantPrice.variantThree',
            'productVariant' => function($variantQuery) use ($variant) {
                $variantQuery->where('variant', $variant);
            }])
        ->where(function($query) use ($date, $title) {
            $query->whereDate('created_at', $date)
            ->orWhere('title', 'like', '%' . $title . '%');
        })
        ->paginate(2)->onEachSide(0);
        
        $productVariants = Variant::with('variantTag')->get();
        // dd($products);
        return view('products.index', compact('products','productVariants'));
    }
}
