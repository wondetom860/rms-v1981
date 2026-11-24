<?php

namespace frontend\controllers;

use backend\models\Student;
use backend\models\SemesterRegistration2;

class PreRequisitController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
