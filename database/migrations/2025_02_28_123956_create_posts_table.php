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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('summary')->nullable();
            $table->string('featured_image')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->integer('views')->default(0);
            $table->string('locale', 10)->default('en');
            $table->foreignId('original_post_id')->nullable()->comment('Reference to the original post when this is a translation');
            $table->timestamps();
            $table->timestamp('content_updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
