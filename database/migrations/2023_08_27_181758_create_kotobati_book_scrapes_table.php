<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kotobati_book_scrapes', function (Blueprint $table) {
            $table->id();
            $table->string('book_id')->nullable();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('image', 1000)->nullable();
            $table->string('pages_count')->nullable();
            $table->string('lang')->nullable();
            $table->string('size')->nullable();
            $table->string('download_link', 1000)->nullable();
            $table->string('source_link', 1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kotobati_book_scrapes');
    }
};
