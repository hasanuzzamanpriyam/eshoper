<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Model\Banner;
use App\Model\Blog;
use App\Model\BusinessSetting;
use App\Model\HelpTopic;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(
        private BusinessSetting $business_settings,
    ) {

    }
    public function helpTopic()
    {
        $helps = HelpTopic::Status()->latest()->get();
        $page_title_banner = $this->business_settings->where('type', 'banner_faq_page')->whereJsonContains('value', ['status' => '1'])->first('value');
        return view(VIEW_FILE_NAMES['faq'], compact('helps','page_title_banner'));
    }

    public function contacts()
    {
        $recaptcha = \App\CPU\Helpers::get_business_settings('recaptcha');
        return view(VIEW_FILE_NAMES['contacts'],compact('recaptcha'));
    }

    public function about_us()
    {
        $about_us = BusinessSetting::where('type', 'about_us')->first();
        $page_title_banner = $this->business_settings->where('type', 'banner_about_us')->whereJsonContains('value', ['status' => '1'])->first('value');
        return view(VIEW_FILE_NAMES['about_us'], [
            'about_us' => $about_us,
            'page_title_banner' => $page_title_banner,
        ]);
    }

    public function termsand_condition()
    {
        $page_title_banner = $this->business_settings->where('type', 'banner_terms_conditions')->whereJsonContains('value', ['status' => '1'])->first('value');
        $terms_condition = BusinessSetting::where('type', 'terms_condition')->first();
        return view(VIEW_FILE_NAMES['terms_conditions_page'], compact('terms_condition','page_title_banner'));
    }

    public function privacy_policy()
    {
        $page_title_banner = $this->business_settings->where('type', 'banner_privacy_policy')->whereJsonContains('value', ['status' => '1'])->first('value');
        $privacy_policy = BusinessSetting::where('type', 'privacy_policy')->first();
        return view(VIEW_FILE_NAMES['privacy_policy_page'], compact('privacy_policy','page_title_banner'));
    }

    public function refund_policy()
    {
        $refund_policy = json_decode(BusinessSetting::where('type', 'refund-policy')->first()->value);
        if(!$refund_policy->status){
            return back();
        }
        $refund_policy = $refund_policy->content;
        $page_title_banner = $this->business_settings->where('type', 'banner_refund_policy')->whereJsonContains('value', ['status' => '1'])->first('value');
        return view(VIEW_FILE_NAMES['refund_policy_page'], compact('refund_policy','page_title_banner'));
    }

    public function return_policy()
    {
        $return_policy = json_decode(BusinessSetting::where('type', 'return-policy')->first()->value);
        if(!$return_policy->status){
            return back();
        }
        $return_policy = $return_policy->content;
        $page_title_banner = $this->business_settings->where('type', 'banner_return_policy')->whereJsonContains('value', ['status' => '1'])->first('value');
        return view(VIEW_FILE_NAMES['return_policy_page'], compact('return_policy','page_title_banner'));
    }

    public function cancellation_policy()
    {
        $cancellation_policy = json_decode(BusinessSetting::where('type', 'cancellation-policy')->first()->value);
        if(!$cancellation_policy->status){
            return back();
        }
        $cancellation_policy = $cancellation_policy->content;
        $page_title_banner = $this->business_settings->where('type', 'banner_cancellation_policy')->whereJsonContains('value', ['status' => '1'])->first('value');
        return view(VIEW_FILE_NAMES['cancellation_policy_page'], compact('cancellation_policy','page_title_banner'));
    }

    public function blogs(Request $request)
    {
        $query = Blog::with('category')->where('status', 1);

        if ($request->has('search') && $request->search != null) {
            $query->where('heading', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != null) {
            $query->where('blog_category_id', $request->category);
        }

        $blogs = $query->latest()->paginate(7);
        $recentPosts = Blog::with('category')->where('status', 1)->latest()->take(5)->get();
        $categories = \App\Model\BlogCategory::where('status', 1)->get();

        $blogWords = [];
        foreach ($blogs as $blog) {
            $words = explode(' ', strtolower($blog->heading));
            foreach ($words as $word) {
                if (strlen($word) > 2) {
                    $blogWords[] = $word;
                }
            }
        }
        $searchWords = array_unique($blogWords);

        $matchedIds = [];
        $productList = \App\Model\Product::active()->pluck('name', 'id');
        foreach ($productList as $id => $name) {
            $lowerName = strtolower($name);
            $nameWords = explode(' ', $lowerName);
            foreach ($searchWords as $searchWord) {
                foreach ($nameWords as $nameWord) {
                    similar_text($searchWord, $nameWord, $percent);
                    if ($percent >= 60) {
                        $matchedIds[] = $id;
                        break 2;
                    }
                }
            }
            if (count($matchedIds) >= 20) break;
        }

        $suggestedProducts = \App\Model\Product::active()
            ->with(['rating', 'reviews'])
            ->whereIn('id', $matchedIds)
            ->inRandomOrder()
            ->take(5)
            ->get();

        if ($suggestedProducts->count() < 5) {
            $remainingCount = 5 - $suggestedProducts->count();
            $additionalProducts = \App\Model\Product::active()
                ->with(['rating', 'reviews'])
                ->whereNotIn('id', $matchedIds)
                ->inRandomOrder()
                ->take($remainingCount)
                ->get();
            $suggestedProducts = $suggestedProducts->merge($additionalProducts);
        }

        $blog_page_banner = Banner::where(['banner_type' => 'Blog Page Banner', 'published' => 1])->where('theme', theme_root_path())->first();

        return view(VIEW_FILE_NAMES['blog_list'], compact('blogs', 'recentPosts', 'categories', 'suggestedProducts', 'blog_page_banner'));
    }

    public function blog_show($slug)
    {
        $blog = Blog::with('category')->where('slug', $slug)->where('status', 1)->firstOrFail();
        $recentPosts = Blog::with('category')->where('status', 1)->latest()->take(5)->get();

        $blogTitleWords = explode(' ', strtolower($blog->heading));
        $blogSlugWords = explode('-', strtolower($blog->slug));
        $searchWords = array_unique(array_filter(array_merge($blogTitleWords, $blogSlugWords)));

        $products = \App\Model\Product::active()->pluck('name', 'id');
        $matchedIds = [];

        foreach ($products as $id => $name) {
            $lowerName = strtolower($name);
            $nameWords = explode(' ', $lowerName);
            
            $matchFound = false;
            foreach ($searchWords as $searchWord) {
                if (strlen($searchWord) <= 2) continue;
                foreach ($nameWords as $nameWord) {
                    similar_text($searchWord, $nameWord, $percent);
                    if ($percent >= 60) {
                        $matchedIds[] = $id;
                        $matchFound = true;
                        break 2;
                    }
                }
            }
        }

        $relatedProducts = \App\Model\Product::active()
            ->with(['rating', 'tags'])
            ->whereIn('id', $matchedIds)
            ->inRandomOrder()
            ->take(8)
            ->get();

        $blog_page_banner = Banner::where(['banner_type' => 'Blog Page Banner', 'published' => 1])->where('theme', theme_root_path())->first();

        return view(VIEW_FILE_NAMES['blog_show'], compact('blog', 'recentPosts', 'relatedProducts', 'blog_page_banner'));
    }
}
