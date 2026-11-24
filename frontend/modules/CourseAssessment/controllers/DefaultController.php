<?php

namespace frontend\modules\CourseAssessment\controllers;

use yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\modules\CourseAssessment\models\CourseOffering2;
use frontend\modules\CourseAssessment\models\CourseOffering2Detail;
use backend\models\CourseOffering2DetailSearch;
// use backend\models\CourseOffering2Search();

/**
 * Default controller for the `CourseAssessment` module
 */
class DefaultController extends Controller
{

	/**
     * @inheritdoc
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
            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
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
    public function actionIndex(){
    	// everything here starts from course offering
    	// $courseOfferingModel = new CourseOffering2();
    	$searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        // return $this->render('index');
    }
}
