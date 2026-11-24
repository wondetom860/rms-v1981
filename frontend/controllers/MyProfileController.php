<?php

namespace frontend\controllers;

use yii;
use yii\filters\VerbFilter;

class MyProfileController extends \yii\web\Controller
{
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
                       'actions' => ['index'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLoadProfileImage(){
    	$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    	$user = \backend\models\User::findOne(['id'=>Yii::$app->user->identity->id]);

    	$image = '\backend\web\uploads\\'.$user->photo_num;

    	echo $image;
    }

}
