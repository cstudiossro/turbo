<?php

namespace cstudios\turbo\behaviors;

use Craft;
use craft\base\Plugin;
use cstudios\turbo\Turbo;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Class CommerceCompatibilityBehavior
 * @package cstudios\turbo\behaviors
 */
class CommerceCompatibilityBehavior extends Behavior
{
    public function events()
    {
        return [
            Turbo::EVENT_COMPATIBILITY_CHECK => 'checkCompatibility'
        ];
    }

    /**
     * Since Craft commerce is kind of incompatible with this
     * plugin we will disable caching when there is something in
     * the cart to save on resources and solve compatibility issues
     *
     * Please note, that We're not planning on supporting Commerce 1
     *
     * @param Event $event
     * @throws InvalidConfigException
     */
    public function checkCompatibility(Event $event){

        /** @var Turbo $turbo */
        $turbo = $event->sender;

        /** @var Plugin|null $commerce */
        $commerce = Craft::$app->plugins->getPlugin('commerce');

        /**
         * If we don't even find commerce we just skip ahead
         */
        if ($commerce) {

            $isCommerceInstalled = Craft::$app->plugins->isPluginInstalled('commerce');

            /**
             * We only have to run these scripts if
             * commerce is installed. Otherwise it's pointless
             */
            if ($isCommerceInstalled) {

                /** @var craft\commerce\services\Carts $cartService */
                $cartService = Craft::createObject('craft\commerce\services\Carts');
                $cart = $cartService->cart;

                if ($cart)
                    $turbo->attachingIsUnsafe = !$cart->isEmpty;
            }
        }
    }


}