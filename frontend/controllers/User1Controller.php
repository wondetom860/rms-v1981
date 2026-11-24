<?php

namespace frontend\controllers;

use Yii;
use backend\models\Student1;
use backend\models\Student1Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\User;
use common\models\User1;
use yii\widgets\ActiveForm;

class User1Controller extends \yii\web\Controller
{
    public function actionAdmitStudentUser()
    {
    	if (Yii::$app->user->can('Admin')) {
    		return $this->render('admit-student-user');
    	}else{
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
        
    }

    public function actionValidate(){

    }

    private function checIfExists($model){
        return User::find()
            ->where([
                'first_name'=>$model->first_name,
                'middle_name'=>$model->middle_name,
                'last_name'=>$model->last_name,
            ])
            ->count() > 0;
    }

    public function actionCreate(){
    	$model = new User1();
    	
    	// echo "<pre>";var_dump($_POST);die();
    	

    	if ($model->load(Yii::$app->request->post())) {
            $model->first_name = ucfirst($model->first_name);
            $model->middle_name = ucfirst($model->middle_name);
            $model->last_name = ucfirst($model->last_name);
            
    		$userAccount = new User();
	    	$model->OTP = $this->getOTP();
	    	$model->status = $model::STATUS_UNAPPROVED;
	    	// $model->created_by = Yii::$app->user->identity->id;
	    	$model->created_at = date('Y-m-d h:i:s');

	    	$model->username = md5($model->OTP);

    		// $model->username = $this->getUsername();

	        $model->setPassword($this->getPassword());
	        $model->generateAuthKey();

            if ($this->checIfExists($model)) {
                echo "<pre>";
                echo "Such User Information is already Exists, Please contact registrar if this is mistake!";die();
            }

	        if ($model->save()) {

	        	echo json_encode([1,$model->id,$model->OTP]);
                $model = NULL;
	        	return;
	        }else{
	        	echo "<pre>";
	        	var_dump($model->getErrors());die();
	        }

    	}else{
    		echo "<p>Need New to create?</p>";
    		die();
    	}
    }

    private function getOTP(){//25
        // str_pad(input, 3);
    	$rr = rand(99999,100000000);
    	$dd = date('Ymdhis');
    	$OTP = '';
    	$app = $rr.$dd;
    	for ($i=0; $i < 20; $i++) {$OTP .= $app[$i];}
    	return strtoupper('S-'. $OTP .'-WT');
    }


    public function getUsername(){
        return \Yii::$app->params['college_accr'].date('his').rand(9,100);
    }
    
    public function getPassword(){
        return 'wt_p0000';
    }
}
