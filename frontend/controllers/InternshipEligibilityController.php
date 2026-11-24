<?php

namespace frontend\controllers;

use yii;
use \yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\WGroupSetup;

class InternshipEligibilityController extends Controller
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
                      'actions' => ['index','get-gsets','list-students','list-students-print'],
                      'allow' => true,
                      'roles' => ['DepartmentHead','Admin'],
                   ],
               ]
           ]
        ];
    }

    public function actionListStudentsPrint(){
      $this->layout = "printLayout";

      return $this->render('internship-group/student-list',['model' => $_SESSION['modelEligiblity']]);
    }

    public function actionListStudents(){
      $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('INvalID ReqUEst');

      // gsetId is group setup Id of which students are gona checked for being eligible or NOT for internship.
      // to assure this, every gsets where each student was assigned has to checked in order to know
      //      1.all the courses offered for that group are enrolled by the student
      //      2.all the courses have valid grade/have pass grade

      $students = \backend\models\WGroupAssignment::find()  
        ->innerJoin('student','student.id = w_group_assignment.gas_std_relId')
        ->innerJoin('user','user.id = student.std_user_relId')
        ->where([
          'gas_gset_relId' => $gsetId
        ])
        ->orderBy(['user.first_name' => SORT_ASC])
        ->all();

        $_SESSION['modelEligiblity'] = $students;

        return $this->renderPartial('internship-group/student-list',['model' => $students]);
    }

    public function actionGetGsets(){
      $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Academic Year NOT Selected.');
      $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : NULL;
      $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Please Select Semester');

      $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Admission NOT Selected.');
      $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Program NOT Selected.');

      $gsets = WGroupSetup::find()
          ->where([
              'gs_adm_relId' => $admId,
              'gs_prog_relId' => $progId,
              'gs_acadYear_relId' => $acadYearId,
              'gs_sem_relId' => $semId,
          ]);
      if (!is_null($clslvlId)) {
          $gsets->andWhere(['gs_clslvl_relId' => $clslvlId]);
      }

      if (Yii::$app->user->can('DepartmentHead')) {
        $instDet = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id])->inst_dep_relId;

        $gsets->andWhere(['gs_dept_relId' => $instDet]);
      }

      $gsets->andWhere(['IS NOT','gs_internship',NULL]);
      
      return $this->renderPartial('internship-group/gset-list',['gsetModels' => $gsets->all()]);
    }

    public function actionIndex()
    {    	
      return $this->render('index',['model' => new WGroupSetup()]);
    }

}
