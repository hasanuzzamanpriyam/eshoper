<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Model\Blog;
use App\Model\BusinessSetting;
use App\Model\BlogCategory;
use Illuminate\Http\Request;

class BlogSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Blog::with('category')->where('status', 1);

        if ($request->has('search') && $request->search != null) {
            $query->where('heading', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != null) {
            $query->where('blog_category_id', $request->category);
        }

        $blogs = $query->latest()->paginate(6);
        
        // Return only the blog list portion
        return view('web-views.partials._blog-list-ajax', compact('blogs'))->render();
    }
}
