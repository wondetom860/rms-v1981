<?php

namespace frontend\modules\SemesterEvaluation\controllers;

use yii\web\Controller;

/**
 * Default controller for the `SemesterEvaluation` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect('w-evaluation-types/')->send();
    }
}
