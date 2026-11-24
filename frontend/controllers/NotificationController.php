<?php

namespace frontend\controllers;
use \common\components\NotificationController as ALERT;
use yii\filters\VerbFilter;

class NotificationController extends \yii\web\Controller
{
	/**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

        //     'access' => [
        //        'class' => \yii\filters\AccessControl::class,
        //        // We will override the default rule config with the new AccessRule class
        //        'ruleConfig' => [
        //            'class' => \yii\filters\AccessRule::class,
        //        ],
        //        'rules' => [
        //            [
        //                'actions' => [],
        //                'allow' => true,
        //                'roles' => ['@'],
        //            ],
        //        ]
        //    ]
        ];
    }

	public $enableCsrfValidation = false;
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetNotification(){ //GetNotification
    	ALERT::alert();
    	return $this->renderPartial('notifications'); 

    }

}

// notification:{
//     user:{}
//     task:''
//     remote_access:{}
//     time:''
// }