<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category=Category::paginate(10);
        return $this->successRes([
            'Category'=>CategoryResource::collection($category->load(['parent','children'])),
            'links'=>CategoryResource::collection($category)->response()->getData()->links,
            'meta'=>CategoryResource::collection($category)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validate=Validator::make($request->all(),[
            'name'=>'required',
            'parent_id'=>'required|integer',
        ]);
        if ($validate->fails()) {
            return $this->errorRes($validate->messages(),422);
        }
        DB::beginTransaction();
        $category=Category::create($request->only(['name','parent_id']));
        DB::commit();
        return $this->successRes(new CategoryResource($category),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->successRes(new CategoryResource($category->load(['parent','children'])),200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validate=Validator::make($request->all(),[
            'name'=>'required',
            'parent_id'=>'required',
        ]);
        if ($validate->fails()) {
            return $this->errorRes($validate->messages(),422);
        }
        $category=Category::find($category->id);
        DB::beginTransaction();
        $category->update($request->only(['name','display_name']));
        DB::commit();
        return $this->successRes(new CategoryResource($category),201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->successRes(new CategoryResource($category),200);
    }
}
