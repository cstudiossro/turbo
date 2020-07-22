<?php
/**
 * Page Cache plugin for Craft CMS 3.x
 *
 * This plugin utilizes the PageCache filter from yii2 into your craft 3 instance
 *
 * @link      https://cstudios.sk
 * @copyright Copyright (c) 2020 Gergely Horvath
 */

namespace cstudios\turbo;


use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\App;
use craft\services\Plugins;
use craft\events\PluginEvent;

use craft\web\Application;
use craft\web\UrlManager;
use craft\web\View;
use cstudios\turbo\controllers\SettingsController;
use cstudios\turbo\models\Settings;
use yii\base\Event;

/**
 * Class Turbo
 *
 * @author    Gergely Horvath
 * @package   Turbo
 * @since     1.0.0
 *
 */
class Turbo extends Plugin
{

    /**
     * @var Turbo
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    public $id = 'turbo';

    public $handle = 'turbo';

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    public $controllerMap = [
        'settings' => 'cstudios\turbo\controllers\SettingsController'
    ];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'cstudios\\turbo\\console\\controllers';
        } else {
            $this->controllerNamespace = 'cstudios\\turbo\\controllers';
        }

        Event::on(
            Application::class,
            Application::EVENT_BEFORE_ACTION,
            function ($event){

                $enabled = $this->getSettings()->enabled;
                $cacheVersion = $this->getSettings()->cacheVersion;
                $durationInMinutes = $this->getSettings()->durationInMinutes;

                if ($enabled && !Craft::$app->request->isCpRequest){
                    Craft::$app->controller->attachBehavior('turbo',[
                        'class' => 'yii\filters\PageCache',
                        'duration' => $durationInMinutes*60,
                        'variations' => [
                            $cacheVersion,
                            Craft::$app->request->fullPath,
                            Craft::$app->request->get(),
                        ],
                    ]);
                }


            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event){
                $event->rules['turbo/<controller>/<action>'] = 'turbo/<controller>/<action>';
            }
        );

        Craft::info(
            Craft::t(
                'turbo',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * @return bool|Settings|null
     */
    public function getSettings()
    {
        return parent::getSettings();
    }

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate('turbo/settings', [
            'settings' => $this->getSettings()
        ]);
    }

}
