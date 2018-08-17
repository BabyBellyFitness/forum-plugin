<?php namespace BabyBellyFitness\Forum\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use BabyBellyFitness\Forum\Models\Channel;
use BabyBellyFitness\Forum\Models\Member as MemberModel;
use BabyBellyFitness\Forum\Classes\TopicTracker;

class Channels extends ComponentBase
{
    /**
     * @var BabyBellyFitness\Forum\Models\Member Member cache
     */
    protected $member;

    /**
     * @var BabyBellyFitness\Forum\Models\Channel Channel collection cache
     */
    protected $channels;

    /**
     * @var string Reference to the page name for linking to members.
     */
    public $memberPage;

    /**
     * @var string Reference to the page name for linking to topics.
     */
    public $topicPage;

    /**
     * @var string Reference to the page name for linking to channels.
     */
    public $channelPage;

    public function componentDetails()
    {
        return [
            'name'        => 'babybellyfitness.forum::lang.channels.list_name',
            'description' => 'babybellyfitness.forum::lang.channels.list_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'memberPage' => [
                'title'       => 'babybellyfitness.forum::lang.member.page_name',
                'description' => 'babybellyfitness.forum::lang.member.page_help',
                'type'        => 'dropdown',
            ],
            'channelPage' => [
                'title'       => 'babybellyfitness.forum::lang.channel.page_name',
                'description' => 'babybellyfitness.forum::lang.channel.page_help',
                'type'        => 'dropdown',
            ],
            'topicPage' => [
                'title'       => 'babybellyfitness.forum::lang.topic.page_name',
                'description' => 'babybellyfitness.forum::lang.topic.page_help',
                'type'        => 'dropdown',
            ],
        ];
    }

    public function getPropertyOptions($property)
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->addCss('assets/css/forum.css');

        $this->prepareVars();
        $this->page['channels'] = $this->listChannels();
    }

    protected function prepareVars()
    {
        /*
         * Page links
         */
        $this->memberPage = $this->page['memberPage'] = $this->property('memberPage');
        $this->channelPage = $this->page['channelPage'] = $this->property('channelPage');
        $this->topicPage = $this->page['topicPage'] = $this->property('topicPage');
    }

    public function listChannels()
    {
        if ($this->channels !== null) {
            return $this->channels;
        }

        $channels = Channel::with('first_topic')->isVisible()->get();

        /*
         * Add a "url" helper attribute for linking to each channel
         */
        $channels->each(function($channel) {
            $channel->setUrl($this->channelPage, $this->controller);

            if ($channel->first_topic) {
                $channel->first_topic->setUrl($this->topicPage, $this->controller);
            }
        });

        $this->page['member'] = $this->member = MemberModel::getFromUser();

        if ($this->member) {
            $channels = TopicTracker::instance()->setFlagsOnChannels($channels, $this->member);
        }

        $channels = $channels->toNested();

        return $this->channels = $channels;
    }
}
