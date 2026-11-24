<?php

namespace frontend\controllers;
use yii;
use yii\filters\VerbFilter;
use \backend\models\CourseOffering2Search;

class ApproveCourseOfferingController extends \yii\web\Controller
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
               ]
           ]
        ];
    }

    public function actionIndex()
    {
    	if (Yii::$app->user->can('Registrar')) {
    		$searchModel = new CourseOffering2Search();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]); 		
    	}
        // return $this->render('index');
    }

    public function actionApproveOffering(){
    	$id = isset($_POST['id']) && !empty($_POST['id'])?intval($_POST['id']):die('Offering Entry Error!');

    	if (Yii::$app->user->can('Registrar')) {
    		$item = \backend\models\CourseOfferingTemp::findOne($id);

            if ($item->cot_locked == 1) {
                die("Late Operation, Course Offering approval should have to be before group assignement.");
            }

    		if ($item && count($item) > 0) {
    			echo Yii::$app->db->createCommand()->update('course_offering_temp',
    				[
    					'cot_registrarApproved'=>1,
    					'cot_registrarApproved_at'=>date("Y-m-d h:i:s"),
    					'cot_registrarApproved_by'=>Yii::$app->user->identity->id,
    				],
    				[
    					'id'=>$id,
    				])->execute();
    		}else{
    			die("Such Entry Not found.");
    		}
    	}else{
    		die("Unprivilleged Operation");
    	}
    }

}
