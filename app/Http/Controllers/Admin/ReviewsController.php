<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\CPU\Helpers;
use App\Model\Review;
use App\Model\Product;
use App\CPU\ProductManager;
use App\Exports\CustomerReviewListExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Customer;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;

class ReviewsController extends Controller
{
    function list(Request $request)
    {
        $query_param = [];
        if (!empty($request->from) && empty($request->to)) {
            Toastr::warning(translate('please_select_to_date'));
        }
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $product_id = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $customer_id = User::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $reviews = Review::WhereIn('product_id',  $product_id)->orWhereIn('customer_id', $customer_id);
            $query_param = ['search' => $request['search']];
        } else {
            $reviews = Review::with(['product', 'customer'])
                ->when($request->product_id != 0, function ($q) {
                    $q->where('product_id', request('product_id'));
                })->when($request->customer_id != 'all' && $request->customer_id != null, function ($q) {
                    $q->where('customer_id', request('customer_id'));
                })->when($request->status != null, function ($q) {
                    $q->where('status', request('status'));
                })->when($request->from && $request->to, function ($q) use ($request) {
                    $q->whereBetween('created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
                });
        }
        $reviews = $reviews->whereNull('delivery_man_id')
                            ->latest('created_at')
                            ->paginate(Helpers::pagination_limit())
                            ->appends([
                                'search' => $request['search'],
                                'product_id'=>$request['product_id'],
                                'customer_id',$request->customer_id,
                                'status'=>$request->status,
                                'from'=>$request->from,
                                'to'=>$request->to
                                ]);;
        $products = Product::active()->with(['category','brand','seller'])->paginate(20);
        $product = Product::find($request->product_id);
        $customer = "all";
        if($request->customer_id != 'all' && !is_null($request->customer_id) && $request->has('customer_id')){
            $customer =User::find($request->customer_id);
        }
        $customer_id = $request['customer_id'];
        $product_id = $request['product_id'];
        $status = $request['status'];
        $from = $request->from;
        $to = $request->to;

        return view('admin-views.reviews.list', compact('reviews', 'search', 'products', 'product','customer', 'from', 'to', 'customer_id', 'product_id', 'status'));
    }
    public function export(Request $request)
    {
        $product_id = $request['product_id'];
        $customer_id = $request['customer_id'];
        $status = $request['status'];
        $from = $request['from'];
        $to = $request['to'];

        if ($request->has('search') && $request->search != '') {
            $key = explode(' ', $request['search']);
            $product_id = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $customer_id = User::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $review = Review::WhereIn('product_id',  $product_id)->orWhereIn('customer_id', $customer_id)->get();
        } else {
            $review = Review::with(['product', 'customer'])
                    ->when($product_id != null, function ($q) use ($request) {
                        $q->where('product_id', $request['product_id']);
                    })
                    ->when($customer_id && $customer_id != 'all' , function ($q) use ($request) {
                            $q->where('customer_id', $request['customer_id']);
                    })
                    ->when($status != null, function ($q) use ($request) {
                            $q->where('status', $request['status']);
                    })
                    ->when($to != null && $from != null, function ($query) use ($from, $to) {
                        $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                    })->latest()->get();
        }

        if($review->count()==0){
            Toastr::warning(translate('no_data_found_for_export'));
            return back();
        }
        $product_name = "all_products";

        if($request->has('product_id') ){
            $product_name = Product::find($request['product_id'])->value('name');
        }
        $customer_name = "all_customers";
        if($request->has('customer_id')){
            $customer_name = User::find($request['customer_id']);
        }

        $data = [
            'reviews' => $review,
            'product_name' => $product_name,
            'customer_name' => $customer_name,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'key' => $request['search'],

        ];
        return Excel::download(new CustomerReviewListExport($data), 'Customer-Review-List.xlsx');
    }
    public function status(Request $request)
    {
        $review = Review::find($request->id);
        $review->status = $request->status ?? 0;
        $review->save();

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => translate('review_status_updated.')
            ]);
        }

        Toastr::success(translate('review_status_updated'));
        return back();
    }
    public function get_customers(Request $request){
        $key = explode(' ', $request['q']);
        $data = DB::table('users')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            })
            ->where('id','!=', 0)
            ->whereNotNull(['f_name', 'l_name', 'phone'])
            ->limit(20)
            ->get([DB::raw('id,IF(id <> "0", CONCAT(f_name, " ", l_name, " (", phone ,")"),CONCAT(f_name, " ", l_name)) as text')]);

        return response()->json($data);

    }
    /**
     * Search product
     */
    public function search_product(Request $request){
        $key = $request->has('name') ? explode(' ', $request['name']) : [];
        $products = Product::active()->with(['brand','category','seller.shop'])
            ->when(count($key) > 0, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                });
            })
            ->paginate(20);

        return response()->json([
            'result' => view('admin-views.partials._search-product', compact('products'))->render(),
            'hasMore' => $products->hasMorePages(),
        ]);
    }

    public function create()
    {
        return view('admin-views.reviews.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_sku' => 'required',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $product = Product::where('code', $request->product_sku)->first();

        if (!$product) {
            Toastr::error(translate('product_not_found_with_this_sku'));
            return back()->withInput();
        }

        $review = new Review();
        $review->product_id = $product->id;
        if (!$request->customer_id) {
            $review->customer_id = DB::table('users')->where('id', '!=', 0)->inRandomOrder()->value('id');
        } else {
            $review->customer_id = $request->customer_id;
        }
        $review->reviewer_name = $request->reviewer_name;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->status = $request->status ?? 1;
        if ($request->created_at) {
            $review->created_at = \Carbon\Carbon::parse($request->created_at);
            $review->updated_at = \Carbon\Carbon::parse($request->created_at);
        }

        if ($request->hasFile('reviewer_image')) {
            $review->reviewer_image = \App\CPU\ImageManager::upload('profile/', 'webp', $request->file('reviewer_image'));
        }
        
        if ($request->hasFile('attachment')) {
            $images = [];
            foreach ($request->file('attachment') as $img) {
                $images[] = \App\CPU\ImageManager::upload('review/', 'webp', $img);
            }
            $review->attachment = json_encode($images);
        }

        $review->save();

        Toastr::success(translate('review_added_successfully'));
        return redirect()->route('admin.reviews.list');
    }

    public function edit($id)
    {
        $review = Review::findOrFail($id);
        return view('admin-views.reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_sku' => 'required',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $product = Product::where('code', $request->product_sku)->first();

        if (!$product) {
            Toastr::error(translate('product_not_found_with_this_sku'));
            return back()->withInput();
        }

        $review = Review::findOrFail($id);
        $review->product_id = $product->id;
        
        if (!$request->customer_id) {
            // Keep current customer if already assigned, or assign a random one if not
            if (!$review->customer_id) {
                $review->customer_id = DB::table('users')->where('id', '!=', 0)->inRandomOrder()->value('id');
            }
        } else {
            $review->customer_id = $request->customer_id;
        }

        $review->reviewer_name = $request->reviewer_name;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->status = $request->status ?? 1;

        if ($request->created_at) {
            $review->created_at = \Carbon\Carbon::parse($request->created_at);
            $review->updated_at = \Carbon\Carbon::parse($request->created_at);
        }

        if ($request->hasFile('reviewer_image')) {
            if ($review->reviewer_image) {
                \App\CPU\ImageManager::delete('profile/' . $review->reviewer_image);
            }
            $review->reviewer_image = \App\CPU\ImageManager::upload('profile/', 'webp', $request->file('reviewer_image'));
        } elseif (!$request->has('existing_reviewer_image') && $request->is_existing_reviewer_image_removed == 1) {
            if ($review->reviewer_image) {
                \App\CPU\ImageManager::delete('profile/' . $review->reviewer_image);
            }
            $review->reviewer_image = null;
        }

        // Manage attachments (merge existing ones and any newly uploaded ones)
        $existing_images = $request->input('existing_attachment', []);
        
        $new_images = [];
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $img) {
                if ($img->isValid()) {
                    $new_images[] = \App\CPU\ImageManager::upload('review/', 'webp', $img);
                }
            }
        }

        $all_images = array_merge($existing_images, $new_images);
        $review->attachment = !empty($all_images) ? json_encode($all_images) : null;

        $review->save();

        Toastr::success(translate('review_updated_successfully'));
        return redirect()->route('admin.reviews.list');
    }

    public function bulk_import_index()
    {
        return view('admin-views.reviews.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        $request->validate([
            'reviews_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $collections = (new FastExcel)->import($request->file('reviews_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('You have uploaded a wrong format file, please upload the right file.'));
            return back();
        }

        $data = [];
        $error_skus = [];
        $success_count = 0;

        // Fetch one random customer ID as a fallback for bulk-imported reviews
        // (customer_id column is NOT NULL at DB level)
        $fallback_customer_id = DB::table('users')->where('id', '!=', 0)->inRandomOrder()->value('id');

        foreach ($collections as $collection) {
            // Normalize keys: trim, lowercase, and replace spaces with underscores
            $row = [];
            foreach ($collection as $key => $value) {
                $normalized_key = str_replace(' ', '_', strtolower(trim($key)));
                $row[$normalized_key] = $value;
            }

            if (empty($row['product_sku'])) {
                continue;
            }

            $sku = trim((string)$row['product_sku']);

            // Find product by code (SKU)
            $product = Product::where('code', $sku)->first();
            
            if (!$product) {
                $error_skus[] = $sku;
                continue;
            }

            $review_date = $row['review_date'] ?? null;
            $created_at = now();

            if ($review_date) {
                try {
                    if ($review_date instanceof \DateTime) {
                        $created_at = Carbon::instance($review_date);
                    } else {
                        $created_at = Carbon::createFromFormat('d-m-Y', trim($review_date));
                    }
                } catch (\Exception $e) {
                    $created_at = now();
                }
            }

            $data[] = [
                'product_id' => $product->id,
                'customer_id' => $fallback_customer_id,
                'reviewer_name' => isset($row['reviewer_name']) && $row['reviewer_name'] !== '' ? $row['reviewer_name'] : null,
                'rating' => isset($row['rating']) && $row['rating'] !== "" ? (int)$row['rating'] : 5,
                'comment' => $row['comment'] ?? null,
                'status' => isset($row['status']) && $row['status'] !== "" ? (int)$row['status'] : 1,
                'created_at' => $created_at,
                'updated_at' => now(),
            ];
            $success_count++;

            // Batch insert every 100 rows to avoid memory issues if file is large
            if (count($data) >= 100) {
                DB::table('reviews')->insert($data);
                $data = [];
            }
        }
        
        if (count($data) > 0) {
            DB::table('reviews')->insert($data);
        }

        if (count($error_skus) > 0) {
            $unique_errors = array_unique($error_skus);
            Toastr::warning($success_count . ' ' . translate('reviews_imported_successfully') . '. ' . count($unique_errors) . ' ' . translate('invalid_SKUs_skipped') . ': ' . implode(', ', array_slice($unique_errors, 0, 10)) . (count($unique_errors) > 10 ? '...' : ''));
        } else {
            Toastr::success($success_count . ' ' . translate('reviews_imported_successfully'));
        }
        
        return back();
    }

    public function download_template()
    {
        $storage = [
            [
                'product_sku' => 'SKU-001',
                'reviewer_name' => 'John Doe',
                'rating' => 5,
                'comment' => 'Great product!',
                'review_date' => '16-05-2026',
                'status' => 1,
            ]
        ];
        return (new FastExcel($storage))->download('review_bulk_format.xlsx');
    }
}
