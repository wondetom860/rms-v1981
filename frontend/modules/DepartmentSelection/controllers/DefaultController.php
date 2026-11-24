<?php

namespace frontend\modules\DepartmentSelection\controllers;

use yii\web\Controller;

/**
 * Default controller for the `DepartmentSelection` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->can('Student')) {
            return $this->redirect('DepartmentSelection/department-select-entry/');
        }
        return $this->render('index');
    }
}
