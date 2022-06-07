<?php

namespace cstudios\turbo\services;

use Craft;
use craft\base\Component;
use cstudios\turbo\Turbo;

/**
 * Class TurboService
 * @package cstudios\turbo\services
 */
class TurboService extends Component
{
    /**
     * @var Turbo|null
     */
    public $plugin;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
    }

    public function renderDynamic($twigString){
        return Craft::$app->view->renderDynamic("return Craft::\$app->view->renderString('{{ ".$twigString." }}');");
    }

    public function csrfInput()
    {
        return Craft::$app->view->renderDynamic("return Craft::\$app->view->renderString('{{ csrfInput() }}');");
    }
}