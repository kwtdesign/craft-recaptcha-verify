<?php

namespace craftrecaptchaverify;

use Craft;
use craft\base\Plugin;
use craft\contactform\models\Submission;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Plugins;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use function class_exists;
use craftrecaptchaverify\services\Recaptcha as RecaptchaService;
use craftrecaptchaverify\variables\CraftRecaptchaVerifyVariable;
use yii\base\Event;
use yii\base\ModelEvent;

class CraftRecaptchaVerify extends Plugin
{
    public static $plugin;

    public $schemaVersion = '1.0.0';

    public $hasCpSettings = true;

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->setComponents([
            'recaptcha' => RecaptchaService::class
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event)
            {
                $variable = $event->sender;

                $variable->set('craftRecaptchaVerify', CraftRecaptchaVerifyVariable::class);
            }
        );

        if ( class_exists('craft\\contactform\\models\\Submission') )
        {
            Event::on(
                \craft\contactform\models\Submission::class,
                \craft\contactform\models\Submission::EVENT_BEFORE_VALIDATE,
                function(ModelEvent $event)
                {
                    $submission = $event->sender;

                    $recaptchaToken = '';

                    if ( is_array($submission->message) )
                    {
                        $recaptchaToken = $submission->message['recaptchaToken'] ?? '';
                    }

                    if ( !(is_string($recaptchaToken) && $this->recaptcha->verifyToken($recaptchaToken)) )
                    {
                        $submission->addError('recaptchaToken', 'Invalid reCAPTCHA.');

                        $event->isValid = false;
                    }
                }
            );
        }
    }

    protected function createSettingsModel()
    {
        return new \craftrecaptchaverify\models\Settings();
    }

    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate('craft-recaptcha-verify/settings', [
            'settings' => $this->getSettings()
        ]);
    }
}
