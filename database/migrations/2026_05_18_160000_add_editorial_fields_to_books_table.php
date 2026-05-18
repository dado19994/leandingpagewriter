<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->text('synopsis')->nullable()->after('description');
            $table->text('excerpt')->nullable()->after('synopsis');
            $table->json('reviews')->nullable()->after('excerpt');
            $table->string('genre')->nullable()->after('status');
        });

        DB::table('books')
            ->select('id', 'title')
            ->orderBy('id')
            ->each(function ($book) {
                DB::table('books')
                    ->where('id', $book->id)
                    ->update(['slug' => Str::slug($book->title).'-'.$book->id]);
            });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['slug', 'synopsis', 'excerpt', 'reviews', 'genre']);
        });
    }
};
