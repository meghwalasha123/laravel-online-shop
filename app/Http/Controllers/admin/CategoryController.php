<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use DataTables;

// use Image;

class CategoryController extends Controller
{
    public function index(Request $request){

        // $categories = Category::latest();
        
        // if(!empty($request->get('keyword'))){
        //     $categories = $categories->where('name', 'like', '%'.$request->get('keyword').'%');
        // }

        // $categories = $categories->paginate(10);
        // return view('admin.category.list', compact('categories'));

        if ($request->ajax()) {
            $categories = Category::latest();

            return DataTables::of($categories)
                            ->addColumn('status', function(Category $category) {
                                $data['category'] = $category;
                                return view('admin.category.category-status',$data);
                            })
                            ->addColumn('action', 'admin.category.category-action')
                            ->rawColumns(['action'])
                            ->addIndexColumn()
                            ->make(true);
        }
        return view('admin.category.list'); 
    }

    public function create() {
        return view('admin.category.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                $category->image = $newImageName;
                $category->save();

                // Generate Image Thumbnail
                // $sourPath = public_path().'/temp/'.$tempImage->name;
                // $destPath = public_path().'/uploads/category/thumb/'.$newImageName;
                // $img = Image::make($sourPath);
                // $img->resize(300, 200);
                // // $img->fit(450, 600, function ($constraint) {
                // //     $constraint->upsize();
                // // });
                // $img->save($destPath);
             
            }

            $request->session()->flash('success','Category added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category added Successfully'
            ]); 

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }     

    public function edit($categoryId, Request $request){
        $category = Category::find($categoryId);
        if(empty($category)){
            return redirect()->route('categories.index');
        }

        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryId, Request $request){
        $category = Category::find($categoryId);
                
        if(empty($category)){
            $request->session()->flash('error','Category not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'maessage' => 'Category not found'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        if ($validator->passes()) {           
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            $oldImage = $category->image;

            // Save Image Here
            if (!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                // Generate Image Thumbnail
                // $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                // $img = Image::make($sPath);
                // // $img->resize(450, 600);
                // $img->fit(450, 600, function ($constraint) {
                //     $constraint->upsize();
                // });
                // $img->save($dPath);

                $category->image = $newImageName;
                $category->save();

                // Delete Old Images here
                // File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);

            }

            $request->session()->flash('success','Category updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category updated Successfully'
            ]); 

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($categoryId, Request $request){
        $category = Category::find($categoryId);
        if (empty($category)) {
            $request->session()->flash('error','Category not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category not Found'
            ]);
        }

        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);

        $category->delete();

        $request->session()->flash('success','Category Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully'
        ]);
    }
}
