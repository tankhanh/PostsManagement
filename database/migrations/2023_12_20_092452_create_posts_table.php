<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('category_id')->default(0)->comment('0: Root');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->tinyInteger('is_featured')->default(1)->comment('1: Yes - 2: No');
            $table->tinyInteger('status')->default(0)->comment('1: public - 2: private');
            $table->string('image')->nullable();
            $table->longText('excerpt');
            $table->longText('content');
            $table->string('posted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};