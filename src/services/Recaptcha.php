<?php

namespace craftrecaptchaverify\services;

use Craft;
use ReCaptcha\ReCaptcha as ReCaptchaProvider;
use craftrecaptchaverify\CraftRecaptchaVerify;
use craft\base\Component;

class Recaptcha extends Component
{
    public function verifyToken(string $token): bool
    {
        $recaptchaSecretKey = CraftRecaptchaVerify::$plugin->getSettings()->recaptchaSecretKey;

        $ip = Craft::$app->getRequest()->remoteIP;

        if ( !$recaptchaSecretKey )
        {
            Craft::warning(
                'Secret is not configured. Recaptcha will not be validated without one.',
                __METHOD__
            );

            return false;
        }

        $recaptcha = new ReCaptchaProvider($recaptchaSecretKey);

        $recaptchaVerification = $recaptcha->verify($token, $ip);

        if ( !$recaptchaVerification->isSuccess() )
        {
            Craft::error(
                sprintf('Recaptcha verification failed: %s. Please check the error code reference at %s',
                    join(', ', $recaptchaVerification->getErrorCodes()),
                    'https://developers.google.com/recaptcha/docs/verify#error-code-reference'
                ),
                __METHOD__
            );

            return false;
        }

        Craft::trace(
            sprintf('Verified token %s',
                $token
            ),
            __METHOD__
        );

        return true;
    }
}
