<?php

namespace craftrecaptchaverify\models;

use craft\base\Model;

class Settings extends Model
{
    public $recaptchaSiteKey = '';

    public $recaptchaSecretKey = '';
}
