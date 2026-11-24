<?php

namespace frontend\controllers;

class GradeSubissionFormController extends \yii\web\Controller
{
	/**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
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
                       'actions' => ['index','create', 'update', 'delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin'],
                   ],[
                       'actions' => ['index','get-report','get-report-print'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }

    // public function actionIndex()
    // {
    //     return $this->render('index');
    // }

    public function actionGetReportPrint(){
      if (isset($_SESSION['report']) && !empty($_SESSION['report'])) {
        $this->layout = 'printLayout';
        return $this->render('__report_view',['report' => $_SESSION['report']]);
      }
    }

    public function actionGetReport(){//get-report
    	// return 'Getting Report';

    	$adm = isset($_POST['adm']) && !empty($_POST['adm'])?intval($_POST['adm']):die('Uknown Admission');
      $prog = isset($_POST['prog']) && !empty($_POST['prog'])?intval($_POST['prog']):die('Uknown Program');
      $dept = isset($_POST['dept']) && !empty($_POST['dept'])?intval($_POST['dept']):die('Uknown Department');
      $clslvl = isset($_POST['clslvl']) && !empty($_POST['clslvl'])?intval($_POST['clslvl']):die('Uknown Class Level');
      $spec = isset($_POST['spec']) && !empty($_POST['spec'])?intval($_POST['spec']):NULL;
      $limit = isset($_POST['limit']) && !empty($_POST['limit'])?intval($_POST['limit']):NULL;

      $acadYear = isset($_POST['acadYear']) && !empty($_POST['acadYear'])?intval($_POST['acadYear']):die('Uknown Academic Year');
      $sem = isset($_POST['sem']) && !empty($_POST['sem'])?intval($_POST['sem']):die('Uknown Seemster');

      // var_dump($limit);die();

    	$report = $this->getReport($adm,$prog,$dept,$clslvl,$spec,$acadYear,$sem,$limit);

      $_SESSION['report'] = $report;

    	return $this->renderPartial('__report_view',['report' => $report]);
    }

    private function getReport($adm,$prog,$dept,$clslvl,$spec,$acadYear,$sem,$limit){
    	$model = new \frontend\models\SemesterRegistration2Detail();
    	$records = $model->find()
    		->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
    		->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
    		->innerJoin('user','user.id = student.std_user_relId');

    	$records->andFilterWhere([
    		'student.std_admission_relId' => $adm,
    		'student.std_prog_relId' => $prog,
    		'student.std_dept_relId' => $dept,
    		'semreg_acadYear_relId' => $acadYear,
    		'semreg_clslvl_relId' => $clslvl,
    		'semreg_semester_relId' => $sem
    	]);
    	if (isset($spec) && !is_null($spec)) {
    		$records->andFilterWhere(['student.std_spec_relId' => $spec]);
    	}
      if (isset($limit) && !is_null($limit)) {
        $records->limit($limit);
      }

    	return $records->orderBy('user.first_name')->all();
    }

    // index
    public function actionIndex()
    {
    	$modelSssh = new \backend\models\StudentSemesterStatusHistory();
        return $this->render('index',['model' => $modelSssh]);
    }

}
