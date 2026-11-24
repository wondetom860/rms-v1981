<?php

namespace frontend\controllers;

use yii;

/**
 * 
 */
class AboutDevsController extends yii\web\Controller
{
	
	public function actionDevs(){
		$id = isset($_POST['id']) && !empty($_POST['id'])?(string)$_POST['id']:die('Information Not available');
		return $this->renderPartial('about-devs',['id'=>$id]);
	}
}

?>