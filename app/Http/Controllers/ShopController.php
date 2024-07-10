<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null){

        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];        

        $categories = Category::orderBy('name','ASC')
        ->with('sub_category')
        ->where('status',1)
        ->get();
        $brands = Brand::orderBy('name', 'ASC')->where('status',1)->get();

        $products = Product::where('status',1);

        // Apply filters here

        if (!empty($categorySlug)) {
            $category = Category::where('slug',$categorySlug)->first();
            $products = $products->where('category_id',$category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug',$subCategorySlug)->first();
            $products = $products->where('sub_category_id',$subCategory->id);
            $subCategorySelected = $subCategory->id;
        }

        if(!empty($request->get('brand'))){
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id',$brandsArray);
        }

        if (!empty($request->get('search'))) {
            $products = $products->where('title','like','%'.$request->get('search').'%');
        }

        $products = $products->orderBy('id','DESC');

        $products = $products->get();

        $data['categories'] = $categories;        
        $data['brands'] = $brands;        
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;

        return view('front.shop',$data);
    }

    public function product($slug){
        $product = Product::where('slug', $slug)
                        ->withCount('product_ratings')
                        ->withSum('product_ratings','rating')
                        ->with(['product_images','product_ratings'])->first();

        // dd($product);               
        if($product == null){
            abort(404);
        }

        $relatedProducts = [];
        //  Fetch Related Product 
        if($product->related_products != ''){
            $productArray = explode(',', $product->related_products);
            
            $relatedProducts = Product::whereIn('id',$productArray)->where('status',1)->get();
        }

        $data['product'] = $product; 
        $data['relatedProducts'] = $relatedProducts; 

        $avgRating = '0.00';
        $avgRatingPercentage = 0;
        // Rating Calculation
        if($product->product_ratings_count > 0){
            $avgRating = number_format(($product->product_ratings_sum_rating/$product->product_ratings_count),2);
            $avgRatingPercentage = ($avgRating*100)/5;
        }

        $data['avgRating'] = $avgRating; 
        $data['avgRatingPercentage'] = $avgRatingPercentage; 

        return view('front.product',$data);
    }

    public function saveRating($id, Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:4',
            'email' => 'required|email',
            'comment' => 'required|min:10',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        } 

        $count = ProductRating::where('email',$request->email)->count();
        
        if($count > 0){
            session()->flash('error', 'you already rated this product.');
            return response()->json([
                'status' => true
            ]);
        }

        $productRating = new ProductRating;
        $productRating->product_id  = $id;
        $productRating->username = $request->name;
        $productRating->email = $request->email;
        $productRating->comment = $request->comment;
        $productRating->rating = $request->rating;
        $productRating->status = 0;
        $productRating->save();

        $message = "Thanks for your rating.";
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
    
}
