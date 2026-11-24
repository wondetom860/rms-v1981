<?php

namespace frontend\controllers;

// ini_set("memory_limit", "1024M");
class GradeSummeryController extends \yii\web\Controller
{
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
                       'actions' => ['index','create', 'update', 'delete','letter-grade-summary'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin'],
                   ],[
                       'actions' => ['index','get-report','get-report-print','letter-grade-summary'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }
    public function actionGetLetteGradeStatisticsReportPrint(){//get-lette-grade-statistics-print
      if (isset($_SESSION['reportModel']) && !empty($_SESSION['reportModel'])) {
        $this->layout = 'printLayout';
        $_SESSION['report'] = "Grade Submission Summery";
        return $this->render('__report_view_letter_grade_statistics',['report' => $_SESSION['reportModel']]);
      }
    }

    public function actionGetLetteGradeStatisticsReport(){//get-lette-grade-statistics
        // return 'Getting Report';

        $adm = isset($_POST['adm']) && !empty($_POST['adm'])?intval($_POST['adm']):die('Uknown Admission');
        $prog = isset($_POST['prog']) && !empty($_POST['prog'])?intval($_POST['prog']):die('Uknown Program');
        $dept = isset($_POST['dept']) && !empty($_POST['dept'])?intval($_POST['dept']):die('Uknown Department');
        $clslvl = isset($_POST['clslvl']) && !empty($_POST['clslvl'])?intval($_POST['clslvl']):die('Uknown Class Level');
        $spec = isset($_POST['spec']) && !empty($_POST['spec'])?intval($_POST['spec']):NULL;
        $group = isset($_POST['group']) && !empty($_POST['group'])?intval($_POST['group']):die('Please Select Group');

        $_SESSION['groupO'] = $group;

        $acadYear = isset($_POST['acadYear']) && !empty($_POST['acadYear'])?intval($_POST['acadYear']):die('Uknown Academic Year');
        $sem = isset($_POST['sem']) && !empty($_POST['sem'])?intval($_POST['sem']):die('Uknown Seemster');

          // var_dump($limit);die();

        $report = $this->getReport($adm,$prog,$dept,$clslvl,$spec,$acadYear,$sem,$group);

        $_SESSION['reportModel'] = $report;

        return $this->renderPartial('__report_view',['report' => $report]);
    }

    public function actionLetterGradeSummary(){//letter-grade-summary
        return $this->render('__letter_grade_summary',['model' => new \backend\models\StudentSemesterStatusHistory()]);
    }
    // __letter_grade_summary

    public function actionGetReportPrint(){
      if (isset($_SESSION['reportModel']) && !empty($_SESSION['reportModel'])) {
        $this->layout = 'printLayout';
        $_SESSION['report'] = "Grade Submission Summery";
        return $this->render('__report_view',['report' => $_SESSION['reportModel']]);
      }
    }

    public function actionGetReport(){//get-report 
    	// return 'Getting Report';

        $adm = isset($_POST['adm']) && !empty($_POST['adm'])?intval($_POST['adm']):die('Uknown Admission');
        $prog = isset($_POST['prog']) && !empty($_POST['prog'])?intval($_POST['prog']):die('Uknown Program');
        $dept = isset($_POST['dept']) && !empty($_POST['dept'])?intval($_POST['dept']):die('Uknown Department');
        $clslvl = isset($_POST['clslvl']) && !empty($_POST['clslvl'])?intval($_POST['clslvl']):die('Uknown Class Level');
        $spec = isset($_POST['spec']) && !empty($_POST['spec'])?intval($_POST['spec']):NULL;
        $group = isset($_POST['group']) && !empty($_POST['group'])?intval($_POST['group']):die('Please Select Group');

        $_SESSION['groupO'] = $group;

        $acadYear = isset($_POST['acadYear']) && !empty($_POST['acadYear'])?intval($_POST['acadYear']):die('Uknown Academic Year');
        $sem = isset($_POST['sem']) && !empty($_POST['sem'])?intval($_POST['sem']):die('Uknown Seemster');

          // var_dump($limit);die();

        $report = $this->getReport($adm,$prog,$dept,$clslvl,$spec,$acadYear,$sem,$group);

        $_SESSION['reportModel'] = $report;

        return $this->renderPartial('__report_view',['report' => $report,'group' => $group]);
    }

    private function getReport($adm,$prog,$dept,$clslvl,$spec,$acadYear,$sem,$group){

        // $gAssignments = \backend\models\WGroupAssignment::find()
        //       ->innerJoin('student','student.id = w_group_assignment.gas_std_relId')
        //       ->innerJoin('w_group_setup','w_group_setup.id = w_group_assignment.gas_gset_relId')
        //       ->innerJoin('user','user.id = student.std_user_relId')
        //       ->where(['gas_gset_relId'=>$group])
        //       ->orderBy('user.first_name')
        //       ->all();

        // if ($gAssignments && count($gAssignments) > 0) {
        //     foreach ($gAssignments as $keyAssignment) {
                
        //     }
        // }

    	$model = new \backend\models\SemesterRegistration2Detail();
    	$records = $model->find()
            ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
    		->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
    		->innerJoin('user','user.id = student.std_user_relId')
            ->where(['srd2_group_relId' => $group]);

    	return $records->orderBy('user.first_name')->all();
    }

    // index
    public function actionIndex()
    {
    	$modelSssh = new \backend\models\StudentSemesterStatusHistory();
        return $this->render('index',['model' => $modelSssh]);
    }

}
