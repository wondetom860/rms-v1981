<?php

namespace frontend\controllers;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii;

/**
 * progress reporter class for continuous assessment.
 */

// ContAsssessProgressReportController
// cont-assess-progress-report
class ContAsssessProgressReportController extends Controller
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
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }
	// $academicYear;
	// $semester;
	public function actionIndex(){
		if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user-> can('Registrar') ||Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk')) {
			return $this->render('index',['model' => new \backend\models\StudentSemesterStatusHistory()]);
		}
	}

	public function actionLoadSubmittedMarks(){
		
	}
}

?>
