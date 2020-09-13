<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrawlUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawl_urls', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('parent')->nullable();
            $table->string('site')->index();
            $table->text('url');
            $table->string('url_hash')->index();

            $table->tinyInteger('has_data')->default(0)->index();
            $table->json('data')->nullable();

            $table->integer('status')->default(0)->index()->comment( 'See Enum\CrawlStatus class');

            $table->integer('visited')->default( 0 );
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
        Schema::dropIfExists('crawl_urls');
    }
}
