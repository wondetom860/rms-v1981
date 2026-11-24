<?php

namespace frontend\controllers;
use yii\helpers\Html;
use yii;
use \backend\models\UserFeedback;

class UserFeedbackController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionForm(){
    	return $this->renderAjax('feedback_form');
    }

    public function actionStatus(){
    	if (isset($_GET['fbId']) && !empty($_GET['fbId'])) {
    		$fbId = intval($_GET['fbId']);

    		$req = UserFeedback::findOne($fbId);

    		if ($req && count($req) > 0) {
    			$result = Yii::$app->db->createCommand()
    				->update('user_feedback',['fb_visible'=>0],['id'=>$fbId])
    				->execute();
    			echo $result;
    		}else{
    			echo "<p class='bg-danger'> Error, No such Entry found.</p>";die();
    		}
    		
    	}
    }

    public function actionStore(){
    	$htmltext = isset($_POST['htmltext']) && !empty($_POST['htmltext'])?$_POST['htmltext']:die('Empty or invalid input');

    	// $htmltext = Html::encode()
    	$htmltext = Html::encode($htmltext);

    	$model = new UserFeedback();
    	$model->fb_created_at = date('Y-m-d h:i:s');
    	$fileName = '';
    	if (Yii::$app->user->isGuest) {
    		$model->fb_user_relId = NULL;

    		$fileName = "feedback_guest_user_".date('Y_m_d__h').rand(99,10000);
    	}else{
    		$model->fb_user_relId = Yii::$app->user->identity->id;
    		$user = \common\models\User1::findOne($model->fb_user_relId);
    		$fileName = "feedback_{$user->first_name}_{$user->middle_name}_".date('Y_m_d__h').rand(99,10000);
    	}

    	$model->fb_rate = 5.0;
    	$ret = $model->saveToFile($htmltext,$fileName);
    	if (is_bool($ret) && $ret == true) {
    		$model->fb_file_name = $fileName;
    		if ($model->save()) {
    			echo 1;
    		}else{
    			var_dump($model->getErrors());
    			die();
    		}
    	}else{
    		echo "<p class='bg-danger'>Error while adding your feedback. Please try again.</p>";
    	} 	
    }

    public function actionGetFeedbacks(){
    	$fbs = \backend\models\UserFeedback::find()->where(['fb_visible'=>1])->all();

    	if ($fbs && count($fbs) > 0) {
    		foreach ($fbs as $key) {
    			$fbDiv = "";

    			$fileName = 'uploads/feedbacks/'.$key->fb_file_name.'.txt';
    			$commenterPhoto = 'uploads/';
    			$commenterName = 'Guest User';

    			if (is_null($key->fb_user_relId)) {
    				$commenterPhoto .= 'avatar.png';
    			}else{
    				$user = \common\models\User1::findOne($key->fb_user_relId);
    				$commenterName = "{$user->first_name} ". strtoupper($user->middle_name[0]);
    				$commenterPhoto .= $user->photo_num;
    			}
    			if (file_exists($commenterPhoto)) {
    				// code...
    			}else{
    				$commenterPhoto = 'uploads/avatar.png';
    			}
    			$fileContent = "Content not found!";
    			try {
    				$handle = fopen($fileName, 'r');
    				$fileContent = fread($handle, filesize($fileName));
    				fclose($handle);
    			} catch (\Exception $e) {
    				$fileContent = "Content not found!";
    			}
    			$btn = "";

    			if (Yii::$app->user->can('Admin')) {
    				$btn = "<button title='Delete this feed back' class='btn btn-warning btn-xs btn-link' onclick='showCommentOptions($key->id)'>X</button>";
    			}

    			$fbDiv .= "<div class='col-md-4'>
			    			<div class='panel panel-default'>
								<div class='panel-heading'>
									<div class='row'>
										<div class='col-md-12'>
											<img src='{$commenterPhoto}' class='img-circle' alt='User Photo' height='38' width='auto' style='border-radius:50%' >
											<span class='pull-right' style='display:inline'>{$btn}
												</span>
											<p style='position:relative; bottom:0px;'>{$commenterName}
											</p>

										</div>
									</div>
								</div>
								<div class='panel-body'>
									".$fileContent."
								</div>
							</div>
						</div>";
    			echo "{$fbDiv}";
    		}
    		die();
    	}else{
    		echo "<p class='bg-danger'>No feed back available.</p>";
    		die();
    	}
    }

}
