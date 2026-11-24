<?php

namespace frontend\controllers;
use yii;
use \backend\models\CourseOffering2Detail;
use \backend\models\CourseOffering2DetailSearch;
use \common\components\CommonControllerFunctions as CCF;

class MarkDistributionReportController extends \yii\web\Controller
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
       return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
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
        $this->__constructor();
    	$searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->acadYearId,$this->semId);
    	$coffdetObject = new CourseOffering2Detail();
        return $this->render('course-offering2-detail-index',
    	[
            'coffdetObject'=>$coffdetObject->getMD(),
        	'searchModel'=>$searchModel,
        	'dataProvider'=>$dataProvider,
        ]);
    }

}

