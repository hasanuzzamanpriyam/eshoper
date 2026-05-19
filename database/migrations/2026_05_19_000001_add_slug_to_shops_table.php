<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Generate slugs for existing shops
        $shops = \App\Model\Shop::all();
        foreach ($shops as $shop) {
            if (empty($shop->slug)) {
                $baseSlug = Str::slug($shop->name);
                $slug = $baseSlug;
                $counter = 1;
                
                while (\App\Model\Shop::where('slug', $slug)->where('id', '!=', $shop->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $shop->slug = $slug;
                $shop->save();
            }
        }
    }

    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
