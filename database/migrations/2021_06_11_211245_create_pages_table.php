<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('group')->default('blog');
            $table->string('title')->index();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('slug');
            $table->string('keywords')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('publish_date')->nullable();
            $table->integer('viewed')->unsigned()->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });

        Schema::create('page_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('page_id')->unsigned();
            $table->string('title')->index();
            $table->string('slug');
            $table->string('related')->nullable();
            $table->integer('clicked')->nullable()->unsigned()->default(0);
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
        Schema::dropIfExists('pages');
        Schema::dropIfExists('page_tags');
    }
}



