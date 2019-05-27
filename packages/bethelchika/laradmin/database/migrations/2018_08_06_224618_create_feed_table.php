<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');//TODO need to change

            /**
             * feed id assigned by the source or feedable source
             *
             * @var string
             */  
            $table->string('source_id')->nullable();//Making this nullable could allow some flexibility?

            /**
             * The feedable type
             *
             * @var string
             */     
            $table->string('source_type')->nullable();//Making this nullable could allow some flexibility?

            /**
             * The title . It should only be accessed through getter so that it can be well formatted and html cleaned
             *
             * @var string
             */
            $table->string('title');

            /**
             * The content. It should only be accessed through getter so that it can be well formatted and html cleaned
             *
             * @var string
             */
            $table->string('content');

             /**
             * The type of feed. Default is 0 @see $types in the model for possible values
             *
             * @var int
             */
            $table->string('type')->default(0);

            /**
             * The css class
             *
             * @var string
             */
            $table->string('css_class')->default(' ');

            /**
             * The type of icon. Default is 'image' @see $sourceIconTypes in the model for possible values
             *
             * @var string
             */
            $table->string('source_icon_type')->nullable();

            /**
             * The icon for the feed srouce
             *
             * @var string
             */
            $table->string('source_icon')->nullable();

            /**
             * The title . It should only be accessed through getter so that it can be well formatted and html cleaned
             *
             * @var string
             */
            $table->string('image')->nullable();

            /**
             * The url for the source
             *
             * @var string
             */
            $table->string('source_url')->default('#');

            /**
             * The link for the feed
             *
             * @var string
             */
            $table->string('url')->nullable();

            /**
             * The name of the feed source
             *
             * @var string
             */
            $table->string('source_name');

            /**
             * The summary of feed . It should only be accessed through getter so that it can be well formatted and html cleaned
             *
             * @var string
             */
            $table->string('summary')->nullable();
            //$table->string('date');

            /**
             * The url to a share page. The page should contain open graph details and other social sharing meta,This should ideally be the home page of the feed i.e = $this->url.
             * 
             * @var string
             */
            $table->string('share_url')->nullable();

            /**
             * HTML to be place somwhat above the feed
             *
             * @var string
             */
            $table->string('before_html')->nullable();

           /**
             * The html to be placed after the feed
             *
             * @var string
             */
            $table->string('after_html')->nullable();

            /**
             * Twitter hash tags
             *
             * @var string
             */
            $table->string('twitter_hashtags')->default('');
            /**
             * Twitter related screen names
             *
             * @var string
             */
            $table->string('twitter_screen_names')->default('');
        
            /**
             * The tweet via parameter
             *
             * @var string
             */
            $table->string('twitter_via')->default('');
        
            /**
             * The language of this feed
             *
             * @var string
             */
            $table->string('lang')->nullable('en');


            $table->timestamps();

            $table->unique(['source_type','source_id']);//NOTE that this can cause issues when a feed source does not have a away of assigning uniqu ids to the feeds it create. It will only be allowed to created one feed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feeds');
    }
}
