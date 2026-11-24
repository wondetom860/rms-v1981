<?php

namespace frontend\controllers;
use yii;

class MarkDistributionController extends \yii\web\Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
       return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
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
                       'actions' => ['index','create', 'update', 'delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@',],
                   ],
               ]
           ]
        ];
    }

    
    public function actionIndex()
    {
    	if (Yii::$app->user->can('Instructor')) {
    		return $this->render('index');
    	}else if(Yii::$app->user->can('DepartmentHead')){
    		return $this->render('indexHoD');
    	}else{
    		return $this->render('indexAnother');
    	}
    }

}
