<?php

namespace frontend\controllers;
use yii;
use backend\models\CoursePrerequisit;
use backend\models\CoursePrerequisitSearch;

class CoursePrerequisiteReportController extends \yii\web\Controller
{
    public function actionIndex()
    {

    	if (Yii::$app->user->can('clerk') || Yii::$app->user->can('Registrar')) {
    		$searchModel = new CoursePrerequisitSearch();
	        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	        $model = new CoursePrerequisit();
	        return $this->render('/course-prerequisit/index', [
	            'searchModel' => $searchModel,
	            'dataProvider' => $dataProvider,
	            'model'=>$model,
	        ]);
    	}
        return $this->render('index');
    }

}

