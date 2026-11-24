<?php

namespace frontend\controllers;
use yii;
use common\components\CommonControllerFunctions as CCF;

class KeyManagementController extends \yii\web\Controller
{
	public function actionGenerateKey(){
		$key = isset($_GET['key']) && !empty($_GET['key']) ? (string)$_GET['key'] : die('INVALID ACCESS, KEY_NOT_IDENTIFIED');
		return CCF::__GENERATE_KEY(sha1($key));
	}

    public function actionIndex()
    {
        return $this->render('index');
    }

}
