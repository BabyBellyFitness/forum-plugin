<?php namespace BabyBellyFitness\Forum\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('bbf_forum_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->integer('topic_id')->unsigned()->index()->nullable();
            $table->integer('member_id')->unsigned()->index()->nullable();
            $table->integer('edit_user_id')->nullable();
            $table->integer('delete_user_id')->nullable();

            // @todo Move to upgrade script
            // $table->dateTime('edited_at')->nullable();
            // $table->dateTime('deleted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bbf_forum_posts');
    }
}
