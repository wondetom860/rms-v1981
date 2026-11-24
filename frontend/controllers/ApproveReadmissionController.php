<?php

namespace frontend\controllers;

class ApproveReadmissionController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	if (\Yii::$app->user->can('Registrar') || \Yii::$app->user->can('DepartmentHead')) {
    		return $this->render('index_approve');
    	}

        return $this->render('index');
    }

    public function actionAssignToGroup(){
    	return $this->render('assign_to_group');
    }

}
