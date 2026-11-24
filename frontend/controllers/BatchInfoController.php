<?php

namespace frontend\controllers;

class BatchInfoController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index',['modelSchYear'=>\backend\models\SchoolYear::find()->all()]);
    }

    public function actionGo(){
    	$id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']):die('Invalid Value');
    	$batchNum = isset($_POST['batchNum']) && !empty($_POST['batchNum']) ? intval($_POST['batchNum']):die('Invalid Batch Number');

    	$model = new \backend\models\SchoolYear();

    	return $model->updateBatch($id,$batchNum);


    }

}
