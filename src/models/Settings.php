<?php

namespace cstudios\turbo\models;

use craft\base\Model;
use cstudios\turbo\Turbo;

class Settings extends Model
{
    public $enabled = 0;

    public $cacheVersion = 1;

    public $durationInMinutes = 2;

    public function rules()
    {
        return [
            [['enabled', 'cacheVersion', 'durationInMinutes'], 'safe'],
            [['cacheVersion'],'required'],
            [['cacheVersion'],'number'],
            [['durationInMinutes'],'number'],
        ];
    }
}