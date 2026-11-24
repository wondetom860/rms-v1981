<?php

namespace common\modules\HRMS;

use yii;
use yii\filters\VerbFilter;

/**
 * HRMS module definition class
 */
class HRMS extends \yii\base\Module
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
                       'actions' => ['index','create', 'update', 'delete','get-update'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin','Registrar','Personel Head'],
                   ],
               ]
           ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'common\modules\HRMS\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
