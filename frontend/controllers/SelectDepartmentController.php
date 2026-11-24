<?php

namespace frontend\controllers;

use Yii;
use frontend\modules\DepartmentSelection\models\DepartmentSelectEntry;
use frontend\modules\DepartmentSelection\models\DepartmentSelectEntrySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class SelectDepartmentController extends Controller
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
                       'actions' => [],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@',],
                   ],
                   [
                       'actions' => ['index','get-setting-entry'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Student',],
                   ],
               ]
           ]
        ];
    }

    
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetSettingEntry()
    {
        $model = new \frontend\modules\DepartmentSelection\models\SelectionSettingEntry();

        $activeSettingEntry = $model->getActiveSettingEntry();

        return $this->renderPartial('setting-index',['settingList'=>$activeSettingEntry]);
    }

}
