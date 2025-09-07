<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('source_name');
            $table->string('source_id')->nullable();
            $table->string('title');
            $table->string('author')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->text('url')->unique();
            $table->text('image_url')->nullable();
            $table->string('category')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('language')->nullable();
            $table->timestamps();

            $table->unique(['source_name','source_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
