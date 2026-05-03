<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Blog;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Added this line

class BlogController extends Controller
{
    public function add_new()
    {
        $blogs = Blog::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.blog.add-new', compact('blogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:blogs', // Added slug validation
        ], [
            'heading.required' => 'Heading is required!',
            'description.required' => 'Description is required!',
            'slug.required' => 'Slug is required!', // Added slug error message
            'slug.unique' => 'Slug must be unique!', // Added slug uniqueness error message
        ]);

        $blog = new Blog;
        $blog->heading = $request->heading;
        $blog->description = $request->description;
        $blog->slug = Str::slug($request->heading); // Generate slug from heading
        $blog->image = ImageManager::upload('blog/', 'webp', $request->file('image'));
        $blog->status = 1;
        $blog->save();

        Toastr::success('Blog added successfully!');
        return back();
    }

    public function view($slug = null)
    {
        if (!$slug) {
            Toastr::error('Invalid blog slug!');
            return back();
        }
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            Toastr::error('Blog not found!');
            return back();
        }
        return view('admin-views.blog.view', compact('blog'));
    }

    public function edit($id)
    {
        $blog = Blog::find($id);
        return view('admin-views.blog.edit', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'heading' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:blogs,slug,'.$id, // Added slug validation, allowing current ID
        ], [
            'heading.required' => 'Heading is required!',
            'description.required' => 'Description is required!',
            'slug.required' => 'Slug is required!', // Added slug error message
            'slug.unique' => 'Slug must be unique!', // Added slug uniqueness error message
        ]);

        $blog = Blog::find($id);
        $blog->heading = $request->heading;
        $blog->description = $request->description;
        $blog->slug = Str::slug($request->heading); // Generate slug from heading
        if ($request->has('image')) {
            $blog->image = ImageManager::update('blog/', $blog->image, 'webp', $request->file('image'));
        }
        $blog->save();

        Toastr::success('Blog updated successfully!');
        return redirect()->route('admin.blog.add-new');
    }

    public function delete(Request $request)
    {
        $blog = Blog::find($request->id);
        if ($blog->image) {
            ImageManager::delete('blog/' . $blog->image);
        }
        $blog->delete();
        return response()->json();
    }
}
