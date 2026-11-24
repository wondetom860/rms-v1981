<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class RemoveSemesterRegistrationEntryController extends Controller
{
	/**
     * @inheritdoc
     */
    public function behaviors()
    {
       return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            'access' => [
               'class' => \yii\filters\AccessControl::className(),
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::className(),
               ],
               'rules' => [
                   [
                       'actions' => ['index','create', 'update', 'delete','view','get-students','get-enrollments','get-more-on-enrollment'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],[
                       'actions' => ['approve-request','view','get-enrollments'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar',],
                   ],
               ]
           ]
        ];
    }

    public function actionGetMoreOnEnrollment(){
    	$semregId = isset($_POST['semregId']) && !empty($_POST['semregId']) ? intval($_POST['semregId']) : die('InvaliD requEts');

    	$semregModel = \backend\models\SemesterRegistration2::findOne($semregId);

    	return $this->renderPartial('__report/more-on-enrollment',['model' => $semregModel]);
    	
    }
    public function actionGetEnrollments(){
    	$stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']) : die('InvaliD requEts');

    	$model = \backend\models\SemesterRegistration2::find()->where(['semreg_std_relId' => $stdId])->all();

    	return $this->renderPartial('__report/enrollments',['model' => $model]);
    }

    public function actionGetStudents(){
    	$search_key_value = isset($_POST['search_key_value']) && !empty($_POST['search_key_value']) ? (string)($_POST['search_key_value']) : die('Invalid request');

    	$students = \backend\models\Student::find()
    		->innerJoin('user','user.id = student.std_user_relId')
    		->where([
    			'OR',
    				['like','std_regId' , $search_key_value],
    				['like','first_name' , $search_key_value],
    				['like','middle_name' , $search_key_value],
    		])
    		->orderBy('first_name')
    		->all();

    	return $this->renderPartial('__report/student-list',['model' => $students]);
    }

    public function actionIndex()
    {
    	if (Yii::$app->user->can('Registrar')) {
    		return $this->render('approve-request');
    	}else if(Yii::$app->user->can('Admin')){
    		return $this->render('index');
    	}else{
    		throw new ForbiddenHttpException("Access Not Allowed!", 1);
    		
    	}
        
    }

}
