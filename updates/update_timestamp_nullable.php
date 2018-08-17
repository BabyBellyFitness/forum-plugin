<?php namespace BabyBellyFitness\Forum\Updates;

use October\Rain\Database\Updates\Migration;
use DbDongle;

class UpdateTimestampsNullable extends Migration
{
    public function up()
    {
        DbDongle::disableStrictMode();

        DbDongle::convertTimestamps('bbf_forum_channels');
        DbDongle::convertTimestamps('bbf_forum_members');
        DbDongle::convertTimestamps('bbf_forum_posts');
        DbDongle::convertTimestamps('bbf_forum_topic_followers');
        DbDongle::convertTimestamps('bbf_forum_topics');
    }

    public function down()
    {
        // ...
    }
}
