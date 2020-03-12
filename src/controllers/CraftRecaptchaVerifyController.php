<?php

namespace craftrecaptchaverify\controllers;

use Craft;
use craftrecaptchaverify\CraftRecaptchaVerify;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class CraftRecaptchaVerifyController extends Controller
{
    protected $allowAnonymous = ['index'];

    public function actionIndex(): Response
    {
        $this->requirePostRequest();

        $recaptchaToken = Craft::$app->getRequest()->getBodyParam('recaptchaToken');

        if ( !$recaptchaToken )
        {
            throw new BadRequestHttpException('No recaptchaToken in request');
        }

        $verification = CraftRecaptchaVerify::getInstance()->recaptcha->verifyToken($recaptchaToken);

        return $this->asJson([
            'status' => ($verification ? 'success' : 'failed')
        ]);
    }
}
