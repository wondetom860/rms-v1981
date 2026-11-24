<?php

namespace frontend\modules\AuthOrg8n\controllers;

use yii\web\Controller;

/**
 * Default controller for the `AuthOrg8n` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect('/AuthOrg8n/auth-org8n/');
    }
}
