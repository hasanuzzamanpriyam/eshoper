<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Add the slug column
            $table->string('slug')->unique()->after('heading'); // Assuming slug should come after heading
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Drop the slug column
            $table->dropUnique('blogs_slug_unique'); // Drop the unique index first
            $table->dropColumn('slug');
        });
    }
}
