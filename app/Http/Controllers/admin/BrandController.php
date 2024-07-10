<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request){
        $brands = Brand::latest('id');

        if(!empty($request->get('keyword'))){
            $brands = $brands->where('name', 'like', '%'.$request->get('keyword').'%');
        }
        $brands = $brands->paginate(10);
        return view('admin.brands.list', compact('brands'));
    }

    public function create() {
        return view('admin.brands.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands'
        ]);

        if ($validator->passes()) {

            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success','Brand added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand added Successfully'
            ]); 

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request){
        $brand = Brand::find($id);
        if(empty($brand)){
            $request->session()->flash('error','Record not found');
            return redirect()->route('brands.index');
        }

        return view('admin.brands.edit',compact('brand'));
    }

    public function update($id, Request $request){

        $brand = Brand::find($id);
        
        if(empty($brand)){
            $request->session()->flash('error','Record not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'maessage' => 'Record not found'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$brand->id.',id',
        ]);

        if ($validator->passes()) {
           
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success','brand updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'brand updated Successfully'
            ]); 

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request){
        $brand = Brand::find($id);
        if (empty($brand)) {
            $request->session()->flash('error','Brand not Found');
            return response()->json([
                'status' => true,
                'message' => 'Brand not Found'
            ]);
        }

        $brand->delete();

        $request->session()->flash('success','Brand Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Brand Deleted Successfully'
        ]);
    }
}
