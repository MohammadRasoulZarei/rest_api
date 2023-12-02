<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brand=Brand::paginate(2);
        return $this->successRes([
            'brand'=>BrandResource::collection($brand),
            'links'=>BrandResource::collection($brand)->response()->getData()->links,
            'meta'=>BrandResource::collection($brand)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate=Validator::make($request->all(),[
            'name'=>'required',
            'display_name'=>'required|unique:brands,display_name',
        ]);
        if ($validate->fails()) {
            return $this->errorRes($validate->messages(),422);
        }
        DB::beginTransaction();
        $brand=Brand::create($request->only(['name','display_name']));
        DB::commit();
        return $this->successRes(new BrandResource($brand),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return $this->successRes(new BrandResource($brand),200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validate=Validator::make($request->all(),[
            'name'=>'required',
            'display_name'=>'required|unique:brands,display_name,'.$brand->id,
        ]);
        if ($validate->fails()) {
            return $this->errorRes($validate->messages(),422);
        }
        $brand=Brand::find($brand->id);
        DB::beginTransaction();
        $brand->update($request->only(['name','display_name']));
        DB::commit();
        return $this->successRes(new BrandResource($brand),201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return $this->successRes(new BrandResource($brand),200);
    }
    public function products(Brand $brand) {
        return $this->successRes(new BrandResource($brand->load('products')));
    }
}
