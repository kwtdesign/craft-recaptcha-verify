<?php

namespace craftrecaptchaverify\variables;

use craft\web\twig\variables\CraftVariable;
use craftrecaptchaverify\CraftRecaptchaVerify;

class CraftRecaptchaVerifyVariable extends CraftVariable
{
    public function getRecaptchaSiteKey(): string
    {
        return CraftRecaptchaVerify::getInstance()->getSettings()->recaptchaSiteKey;
    }
}
