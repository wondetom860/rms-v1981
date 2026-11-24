<?php

namespace frontend\controllers;
use yii\filters\VerbFilter;
use backend\models\CourseOffering2Detail;
use backend\models\CourseOffering2DetailSearch;
use yii;
use \common\components\CommonControllerFunctions as CCF;

class TimeScheduleController extends \yii\web\Controller
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
                       'roles' => ['@','DepartmentHead'],
                   ],
               ]
           ]
        ];
    }
    public function actionIndex()
    {
    	$this->__constructor();
    	if (isset($_GET['CourseOffering2DetailSearch']['acadYear'])) {
    		$this->acadYearId = $_GET['CourseOffering2DetailSearch']['acadYear'];
    	}

    	if (isset($_GET['CourseOffering2DetailSearch']['sem'])) {
    		$this->semId = $_GET['CourseOffering2DetailSearch']['sem'];
    	}

    	// var_dump($this->acadYearId,$this->semId);

    	// die();

    	$searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->acadYearId,$this->semId);

        return $this->render('index_coff2det', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        // return $this->render('index');
    }

}
