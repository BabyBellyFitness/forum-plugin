<?php namespace BabyBellyFitness\Forum;

use Event;
use Backend;
use Bbf\Models\User;
use BabyBellyFitness\Forum\Models\Member;
use System\Classes\PluginBase;
use Bbf\Controllers\Users as UsersController;

/**
 * Forum Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'babybellyfitness.forum::lang.plugin.name',
            'description' => 'babybellyfitness.forum::lang.plugin.description',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-comments',
            'homepage'    => 'https://github.com/rainlab/forum-plugin'
        ];
    }

    public function boot()
    {
        User::extend(function($model) {
            $model->hasOne['forum_member'] = ['BabyBellyFitness\Forum\Models\Member'];

            $model->bindEvent('model.beforeDelete', function() use ($model) {
                $model->forum_member && $model->forum_member->delete();
            });
        });

        UsersController::extendFormFields(function($widget, $model, $context) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof \Bbf\Models\User) {
                return;
            }
            if ($context != 'update') {
                return;
            }
            if (!Member::getFromUser($model)) {
                return;
            }

            $widget->addFields([
                'forum_member[username]' => [
                    'label'   => 'babybellyfitness.forum::lang.settings.username',
                    'tab'     => 'Forum',
                    'comment' => 'babybellyfitness.forum::lang.settings.username_comment'
                ],
                'forum_member[is_moderator]' => [
                    'label'   => 'babybellyfitness.forum::lang.settings.moderator',
                    'type'    => 'checkbox',
                    'tab'     => 'Forum',
                    'span'    => 'auto',
                    'comment' => 'babybellyfitness.forum::lang.settings.moderator_comment'
                ],
                'forum_member[is_banned]' => [
                    'label'   => 'babybellyfitness.forum::lang.settings.banned',
                    'type'    => 'checkbox',
                    'tab'     => 'Forum',
                    'span'    => 'auto',
                    'comment' => 'babybellyfitness.forum::lang.settings.banned_comment'
                ]
            ], 'primary');
        });

        UsersController::extendListColumns(function($widget, $model) {
            if (!$model instanceof \Bbf\Models\User) {
                return;
            }

            $widget->addColumns([
                'forum_member_username' => [
                    'label'      => 'babybellyfitness.forum::lang.settings.forum_username',
                    'relation'   => 'forum_member',
                    'select'     => 'username',
                    'searchable' => false,
                    'invisible'  => true
                ]
            ]);
        });
    }

    public function registerComponents()
    {
        return [
           '\BabyBellyFitness\Forum\Components\Channels'     => 'forumChannels',
           '\BabyBellyFitness\Forum\Components\Channel'      => 'forumChannel',
           '\BabyBellyFitness\Forum\Components\Topic'        => 'forumTopic',
           '\BabyBellyFitness\Forum\Components\Topics'       => 'forumTopics',
           '\BabyBellyFitness\Forum\Components\Member'       => 'forumMember',
           '\BabyBellyFitness\Forum\Components\EmbedTopic'   => 'forumEmbedTopic',
           '\BabyBellyFitness\Forum\Components\EmbedChannel' => 'forumEmbedChannel'
        ];
    }
    
    public function registerPermissions() 
    {
        return [
            'babybellyfitness.forum::lang.settings.channels' => [
                'tab'   => 'babybellyfitness.forum::lang.settings.channels',
                'label' => 'babybellyfitness.forum::lang.settings.channels_desc'
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'babybellyfitness.forum::lang.settings.channels',
                'description' => 'babybellyfitness.forum::lang.settings.channels_desc',
                'icon'        => 'icon-comments',
                'url'         => Backend::url('babybellyfitness/forum/channels'),
                'category'    => 'babybellyfitness.forum::lang.plugin.name',
                'order'       => 500,
                'permissions' => ['babybellyfitness.forum::lang.settings.channels'],
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'babybellyfitness.forum::mail.topic_reply'   => 'Notification to followers when a post is made to a topic.',
            'babybellyfitness.forum::mail.member_report' => 'Notification to moderators when a member is reported to be a spammer.'
        ];
    }
}
