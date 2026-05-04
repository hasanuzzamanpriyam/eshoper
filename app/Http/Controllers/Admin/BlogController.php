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
        $categories = \App\Model\BlogCategory::where('status', 1)->get();
        $blogs = Blog::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.blog.add-new', compact('blogs', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'blog_category_id' => 'required',
            'heading' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:blogs',
        ], [
            'blog_category_id.required' => 'Blog Category is required!',
            'heading.required' => 'Heading is required!',
            'description.required' => 'Description is required!',
            'slug.required' => 'Slug is required!',
            'slug.unique' => 'Slug must be unique!',
        ]);

        $blog = new Blog;
        $blog->blog_category_id = $request->blog_category_id;
        $blog->heading = $request->heading;
        $blog->author_name = $request->author_name;
        $blog->description = $request->description;
        $blog->slug = Str::slug($request->slug);
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
        $blog = Blog::with('category')->where('slug', $slug)->first();
        if (!$blog) {
            Toastr::error('Blog not found!');
            return back();
        }
        return view('admin-views.blog.view', compact('blog'));
    }

    public function edit($id)
    {
        $categories = \App\Model\BlogCategory::where('status', 1)->get();
        $blog = Blog::find($id);
        return view('admin-views.blog.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'blog_category_id' => 'required',
            'heading' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:blogs,slug,'.$id,
        ], [
            'blog_category_id.required' => 'Blog Category is required!',
            'heading.required' => 'Heading is required!',
            'description.required' => 'Description is required!',
            'slug.required' => 'Slug is required!',
            'slug.unique' => 'Slug must be unique!',
        ]);

        $blog = Blog::find($id);
        $blog->blog_category_id = $request->blog_category_id;
        $blog->heading = $request->heading;
        $blog->author_name = $request->author_name;
        $blog->description = $request->description;
        $blog->slug = Str::slug($request->slug);
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

    public function check_slug(Request $request)
    {
        $slug = Str::slug($request->slug);
        $exists = Blog::where('slug', $slug);
        if ($request->has('id')) {
            $exists = $exists->where('id', '!=', $request->id);
        }
        $exists = $exists->exists();

        return response()->json(['exists' => $exists]);
    }
}
