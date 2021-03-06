<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function AllCat() {
        /* ORM */
        //$categories = Category::all();
        // get() se usa para traer todo los datos sin paginacion
        //Para ordenar el ultimo insert de primero
        $categories = Category::latest()->paginate(
            $perPage = 5, $columns = ['*'], $pageName = 'categories'
        );
        // Traer los item en la papelera
        $trashCat = Category::onlyTrashed()->latest()->paginate(
            $perPage = 3, $columns = ['*'], $pageName = 'trashCat'
        );

        /* Query Builder*/
        //$categories = DB::table('categories')->latest()->paginate(5);
        // $categories = DB::table('categories')
        // ->join('users', 'categories.user_id', 'users.id')
        // ->select('categories.*', 'users.name')
        // ->latest()->paginate(5);

        return view('admin.category.index', compact('categories', 'trashCat'));
    }

    public function AddCat(Request $request) {
        $validated = $request->validate([
            'category_name' => 'required|unique:categories|max:255',

        ],
        [
            'category_name.required' => 'Please enter a Category name',
            'category_name.max' => 'Category less than 255Chars',
        ]);

        /* ORM #1 */
        Category::insert([
            'category_name' => $request->category_name,
            'user_id' => Auth::user()->id,
            'created_at' => Carbon::now()

        ]);

        /* ORM #2 esta es al de la documentacion official 9 */
        // $category = new Category;
        // $category->category_name = $request->category_name;
        // $category->user_id = Auth::user()->id;
        // $category->save();

        /* Query Builder */
        // $data = array();
        // $data['category_name'] = $request->category_name;
        // $data['user_id'] = Auth::user()->id;
        // DB::table('categories')->insert($data);

        return Redirect()->back()->with('success', 'Category inserted successfull');

    }

    public function Edit($id) {
        /* Eloquent ORM */
        //$categories = Category::find($id);

        /* Query Builder */
        $categories = DB::table('categories')->where('id', $id)->first();

        return view('admin.category.edit', compact('categories'));
    }

    public function Update(Request $request, $id) {
        /* Eloquent ORM */
        // $update = Category::find($id)->update([
        //     'category_name' => $request->category_name,
        //     'user_id' => Auth::user()->id
        // ]);

        /* Query Builder */
        $data = array();
        $data['category_name'] = $request->category_name;
        $data['user_id'] = Auth::user()->id;
        DB::table('categories')->where('id', $id)->update($data);

        return Redirect()->route('all.category')->with('success', 'Category updated successfull');
    }

    public function SoftDelete($id) {
        $delete = Category::find($id)->delete();

        return Redirect()->back()->with('success', 'Category soft deleted successfull');
    }

    public function Restore($id) {
        $restore = Category::withTrashed()->find($id)->restore();

        return Redirect()->back()->with('success', 'Category restored successfull');
    }

    public function Pdelete($id) {
        $pdelete = Category::onlyTrashed()->find($id)->forceDelete();

        return Redirect()->back()->with('success', 'Category permanently deleted successfull');
    }
}
