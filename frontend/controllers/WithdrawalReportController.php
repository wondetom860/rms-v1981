<?php

namespace frontend\controllers;

class WithdrawalReportController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	if (\Yii::$app->user->can('Student')) {
    		return $this->render('index_student');
    	}else if(\Yii::$app->user->can('Registrar')){
    		return $this->render('index_registrar');
    	}else if(\Yii::$app->user->can('DepartmentHead')){
    		return $this->render('index_HOD');
    	}
        return $this->render('index');
    }



}
