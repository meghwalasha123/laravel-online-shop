<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use Image;

class ProductImageController extends Controller
{
    public function update(Request $request){

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName = $request->product_id.'-'. $productImage->id.'-'.time().'-'.$ext;
        $productImage->image = $imageName;
        $productImage->save();

        // Generate Product Thumbnails
        // Large Image
        $destPath = public_path().'/uploads/product/large'.$imageName;
        $image = Image::make($sourcePath);
        $image->resize(1400, null, function ($constraint) {
             $constraint->aspectRatio();
        });
        $image->save($destPath);

        // Small Image
        $destPath = public_path().'/uploads/product/small'.$imageName;
        $image = Image::make($sourcePath);
        $image->fit(300, 300);
        $image->save($destPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'ImagePath' => asset('uploads/product/small'.$productImage->image),
            'message' => 'Image Saved Successfully'
        ]);

    }
    public function destroy(Request $request){
        $productImage = ProductImage::find($request->id);

        if(empty($productImage)){
            return response()->json([
                'status' => false,
                'message' => 'Image not Found'
            ]);
        }

        // Delete Images From Folder
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image daleted Successfully'
        ]);

    }
}
