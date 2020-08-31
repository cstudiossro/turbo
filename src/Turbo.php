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
use craft\web\Application;
use craft\web\UrlManager;
use cstudios\turbo\behaviors\CommerceCompatibilityBehavior;
use cstudios\turbo\models\Settings;
use yii\base\Event;

/**
 * Class Turbo
 *
 * @author    Gergely Horvath
 * @package   Turbo
 * @since     1.0.0
 *
 *
 * @property-read null|bool|Settings $settings
 */
class Turbo extends Plugin
{

    // Events
    // =========================================================================

    const EVENT_COMPATIBILITY_CHECK = 'compatibilityCheck';

    // Public Properties
    // =========================================================================

    /**
     * @var Turbo
     */
    public static $plugin;

    /**
     * @var string
     */
    public $id = 'turbo';

    /**
     * @var string
     */
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

    /**
     * This will turn true whenever there is a compatibility issue
     * @var bool
     */
    public $attachingIsUnsafe = false;

    /**
     * @var string[]
     */
    public $controllerMap = [
        'settings' => 'cstudios\turbo\controllers\SettingsController'
    ];

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'commerceCompatibilityBehavior' => CommerceCompatibilityBehavior::class,
        ];
    }

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
            function ($event) {

                $enabled = $this->getSettings()->enabled;
                $cacheVersion = $this->getSettings()->cacheVersion;
                $durationInMinutes = $this->getSettings()->durationInMinutes;

                $this->trigger(self::EVENT_COMPATIBILITY_CHECK);

                if ($enabled && !$this->attachingIsUnsafe && !Craft::$app->request->isCpRequest) {
                    Craft::$app->controller->attachBehavior('turbo', [
                        'class' => 'yii\filters\PageCache',
                        'duration' => $durationInMinutes * 60,
                        'variations' => [
                            $cacheVersion,
                            Craft::$app->request->fullPath,
                            Craft::$app->request->get(),
                            Craft::$app->language,
                            Craft::$app->user->isGuest,
                        ],
                    ]);
                }

            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
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
        return Craft::$app->getView()->renderTemplate('turbo/settings', [
            'settings' => $this->getSettings()
        ]);
    }

}
