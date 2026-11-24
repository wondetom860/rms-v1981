<?php

namespace frontend\controllers;
use yii;
use \backend\models\CourseOffering2Detail;
use \backend\models\CourseOffering2DetailSearch;

class CourseAssessmentReportController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	$searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	$coffdetObject = new CourseOffering2Detail();
        return $this->render('course-offering2-detail-index',
    	[
            'coffdetObject'=>$coffdetObject->getMD(),
        	'searchModel'=>$searchModel,
        	'dataProvider'=>$dataProvider,
        ]);
        return $this->render('index');
    }
 
}
