<?php

namespace cstudios\turbo\controllers;

use cstudios\turbo\Turbo;
use craft\helpers\ArrayHelper;
use craft\web\Controller;

class SettingsController extends Controller
{

    public function actionIncreaseCacheVersion()
    {
        $plugin = Turbo::$plugin;
        $settings = $plugin->getSettings();
        $settings->cacheVersion++;

        \Craft::$app->plugins->savePluginSettings($plugin,ArrayHelper::toArray($settings));
        return $this->goBack(\Craft::$app->request->referrer);
    }
}