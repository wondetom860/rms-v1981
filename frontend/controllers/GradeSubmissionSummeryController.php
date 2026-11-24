<?php

namespace frontend\controllers;
use yii\filters\VerbFilter;

class GradeSubmissionSummeryController extends \yii\web\Controller
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
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }
    public function actionIndex()
    {
        if (\Yii::$app->user->can('Registrar') || \Yii::$app->user->can('Admin') || \Yii::$app->user->can('clerk')) {
            $model = new \backend\models\StudentSemesterStatusHistory();
            // return $this->render('index_registrar',);
            return $this->render('index',['model'=>$model]);
        }        
    }

    public function actionLockGrade(){
        if (\Yii::$app->user->can('Registrar')) {
            $coffId = isset($_POST['coffId']) && !empty($_POST['coffId'])? intval($_POST['coffId']):die('Invalid Entry');
            $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])? intval($_POST['courseId']):die('Invalid Entry');
            // $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])? intval($_POST['courseId']):die('Invalid Entry');

            $semreg = new \frontend\models\SemesterRegistration2Detail();
            $opt = intval($_POST['option']);
            // $opt = $opt ? $opt : NULL;
            // echo "{$opt}";
            echo $semreg->lockGrade($coffId,$courseId,$opt); 

            die();
        }else{
            echo "You have no privillege for this action!";
            die();
        }
    }

    public function actionLockGradeSingle(){
        if (\Yii::$app->user->can('Registrar')) {
            $coffId = isset($_POST['coffId']) && !empty($_POST['coffId'])? intval($_POST['coffId']):die('Invalid Entry');
            $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])? intval($_POST['courseId']):die('Invalid Entry');
            // $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])? intval($_POST['courseId']):die('Invalid Entry');

            $semreg = new \frontend\models\SemesterRegistration2Detail();
            $opt = intval($_POST['option']);
            // echo "{$opt}";
            echo $semreg->lockGrade($coffId,$courseId,$opt,1);

            die();
        }else{
            echo "You have no privillege for this action!";
            die();
        }
    }

}

