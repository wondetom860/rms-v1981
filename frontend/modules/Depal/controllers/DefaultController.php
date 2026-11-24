<?php

namespace frontend\modules\Depal\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii;

/**
 * Default controller for the `Depal` module
 */
class DefaultController extends Controller
{

    /**
     * {@inheritdoc}
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
                       'actions' => [],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        // import any previously existing setting
        \frontend\modules\Depal\models\DepalSetting::importSetting();
        \frontend\modules\Depal\models\DepalChoice::importDepartmentChoice();
        if (\Yii::$app->user->can('Student')) {
            return $this->redirect('Depal/depal-choice/');
        }
        return $this->render('index');
    }
}
