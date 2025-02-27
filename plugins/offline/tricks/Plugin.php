<?php namespace OFFLINE\Tricks;

use Event;
use OFFLINE\Tricks\Components\TagList;
use OFFLINE\Tricks\Components\TopicList;
use OFFLINE\Tricks\Components\TrickDetail;
use OFFLINE\Tricks\Components\TrickForm;
use OFFLINE\Tricks\Components\TrickList;
use OFFLINE\Tricks\Console\Seed;
use OFFLINE\Tricks\Models\Tag;
use OFFLINE\Tricks\Models\Topic;
use OFFLINE\Tricks\Models\Trick;
use Winter\User\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['Winter.User'];

    public function registerComponents()
    {
        return [
            TopicList::class   => 'topicList',
            TagList::class     => 'tagList',
            TrickList::class   => 'trickList',
            TrickDetail::class => 'trickDetail',
            TrickForm::class   => 'trickForm',
        ];
    }

    public function registerSettings()
    {
    }

    public function register()
    {
        $this->registerConsoleCommand('tricks.seed', Seed::class);
    }

    public function boot()
    {
        if (class_exists('Winter\User\Models\User')) {
            User::extend(function (User $user) {
                $user->rules['password']              = 'required:create|between:8,255|confirmed';
                $user->rules['password_confirmation'] = 'required_with:password|between:8,255';
                $user->addDynamicMethod('getFallbackAvatarAttribute', function () use ($user) {
                    $url = 'https://www.gravatar.com/avatar/%s.jpg?s=%sd=wavatar';

                    $hashEmail = md5(strtolower(trim($user->email)));
                    return [
                        sprintf($url, $hashEmail, 45),
                        sprintf($url, $hashEmail, 90)
                    ];
                });
            });
        }

        Event::listen('pages.menuitem.listTypes', function () {
            return [
                'all-tricks' => 'All tricks',
                'all-topics' => 'All topics',
                'all-tags'   => 'All tags',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function ($type) {
            if (in_array($type, ['all-tricks', 'all-topics', 'all-tags'])) {
                return [
                    'dynamicItems' => true,
                ];
            }
        });

        Event::listen('pages.menuitem.resolveItem', function ($type, $item, $url, $theme) {
            if ($type === 'all-tricks') {
                return Trick::resolveMenuItem($item, $url, $theme);
            }
            if ($type === 'all-topics') {
                return Topic::resolveMenuItem($item, $url, $theme);
            }
            if ($type === 'all-tags') {
                return Tag::resolveMenuItem($item, $url, $theme);
            }
        });
    }
}
