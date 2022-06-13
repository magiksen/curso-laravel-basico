<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Carbon;

class BrandController extends Controller
{
    public function AllBrand() {
        
        $brands = Brand::latest()->paginate(5);

        return view('admin.brand.index', compact('brands'));
    }

    public function StoreBrand(Request $request) {
        $validated = $request->validate([
            'brand_name' => 'required|unique:brands|min:4',
            'brand_image' => 'required|mimes:jpg,jpge,png,jfif',

        ],
        [
            'brand_name.required' => 'Please enter a Brand name',
            'brand_name.min' => 'Brand longer than 4 characters',
            'brand_image.mimes' => 'Please enter a valid format jpg, jpge, jfif or png'
        ]);

        $brand_image = $request->file('brand_image');

        $name_gen = hexdec(uniqid());
        $image_ext = strtolower($brand_image->getClientOriginalExtension());
        $image_name = $name_gen.'.'.$image_ext;
        $up_location = 'img/brand/';
        $last_img = $up_location.$image_name;

        $brand_image->move($up_location,$image_name);

        Brand::insert([
            'brand_name' => $request->brand_name,
            'brand_image' => $last_img,
            'created_at' => Carbon::now()
        ]);

        return Redirect()->back()->with('success', 'Brand inserted successfully');

    }
}
