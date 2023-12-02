<?php

namespace App\Http\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Http\Resources\ProductResource;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Validator;



class ProductController extends ApiController
{

   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product=Product::paginate(10);
        return $this->successRes([
            'Product'=>ProductResource::collection($product->load(['images','category','brand'])),
            'links'=>ProductResource::collection($product)->response()->getData()->links,
            'meta'=>ProductResource::collection($product)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate=Validator::make($request->all(),[
            'name'=>'required|unique:products,name',
            'brand_id'=>'required',
            'category_id'=>'required',
            'primary_image'=>'required|image',
            'price'=>'required',
            'quantity'=>'required',
            'images.*'=>'image'

        ]);
        if ($validate->fails()) {
            return $this->errorRes($validate->messages(),422);
        }
        $primairyImageName=date('Y_m_d-H_i_s-').rand(1000,9999).$request->primary_image->getClientOriginalName();
        $request->primary_image->move(public_path(env('PRODUCT_IAMGE_PATH')),$primairyImageName);

        $imageNames=[];
        foreach ($request->images as $image) {
            $imageName=date('Y_m_d-H_i_s-').rand(1000,9999).$image->getClientOriginalName();
            $image->move(public_path(env('PRODUCT_IAMGE_PATH')),$imageName);
            $imageNames[]=$imageName;
        }
        $data=$request->only('name','category_id','brand_id','price','quantity','description');
        $data['primary_image']=$primairyImageName;
        DB::beginTransaction();
        $product=Product::create($data);
        foreach ($imageNames as $image) {
            ProductImage::create([
                'product_id'=>$product->id,
                'image'=>$image,
            ]);
        }
        DB::commit();
        return $this->successRes(new ProductResource($product->load(['images','category','brand']),201));



    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->successRes(new ProductResource($product->load('images')),200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validate=Validator::make($request->all(),[
            'name'=>'required',
            'display_name'=>'required|unique:Products,display_name,'.$product->id,
        ]);
        if ($validate->fails()) {
            return $this->errorRes($validate->messages(),422);
        }
        $product=Product::find($product->id);
        DB::beginTransaction();
        $product->update($request->only(['name','display_name']));
        DB::commit();
        return $this->successRes(new ProductResource($product->load('images')),201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->successRes(new ProductResource($product),200);
    }

    function getImages(Product $product) {
        return $this->successRes(new ProductResource($product->load('images')));

    }

}
