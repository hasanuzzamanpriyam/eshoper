<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BlogCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function add_new()
    {
        $categories = BlogCategory::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.blog.category.add-new', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:blog_categories,slug',
        ], [
            'name.required'   => 'Category name is required!',
            'slug.required'   => 'Slug is required!',
            'slug.unique'     => 'Slug must be unique!',
        ]);

        $category = new BlogCategory();
        $category->name   = $request->name;
        $category->slug   = Str::slug($request->slug);
        $category->status = 1;
        $category->save();

        Toastr::success('Blog category added successfully!');
        return back();
    }

    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);
        return view('admin-views.blog.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:blog_categories,slug,' . $id,
        ], [
            'name.required'   => 'Category name is required!',
            'slug.required'   => 'Slug is required!',
            'slug.unique'     => 'Slug must be unique!',
        ]);

        $category = BlogCategory::findOrFail($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        $category->save();

        Toastr::success('Blog category updated successfully!');
        return redirect()->route('admin.blog-category.add-new');
    }

    public function status(Request $request)
    {
        $category = BlogCategory::findOrFail($request->id);
        $category->status = $request->status;
        $category->save();
        return response()->json();
    }

    public function delete(Request $request)
    {
        $category = BlogCategory::findOrFail($request->id);
        $category->delete();
        return response()->json();
    }

    public function check_slug(Request $request)
    {
        $slug = Str::slug($request->slug);
        $exists = BlogCategory::where('slug', $slug);
        if ($request->has('id')) {
            $exists = $exists->where('id', '!=', $request->id);
        }
        return response()->json(['exists' => $exists->exists()]);
    }
}
