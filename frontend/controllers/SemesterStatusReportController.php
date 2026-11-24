<?php

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class SemesterStatusReportController extends Controller
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
                   [
                       'actions' => ['index',''],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Student',],
                   ],
               ]
           ]
        ];
    }

    public function actionGetReportPrint(){
    	$this->layout = 'printLayout';

    	$_POST = isset($_SESSION['post']) && !empty($_SESSION['post']) ? $_SESSION['post'] : die('INvaliD Requset');

    	$adm = isset($_POST['adm']) && !empty($_POST['adm'])?intval($_POST['adm']):die('Please select Admission');
      	$prog = isset($_POST['prog']) && !empty($_POST['prog'])?intval($_POST['prog']):die('Please select Program');
      	$dept = isset($_POST['dept']) && !empty($_POST['dept'])?intval($_POST['dept']):NULL;
      	$clslvl = isset($_POST['clslvl']) && !empty($_POST['clslvl'])?intval($_POST['clslvl']):NULL;
      	$spec = isset($_POST['spec']) && !empty($_POST['spec'])?intval($_POST['spec']):NULL;
      	$group = isset($_POST['group']) && !empty($_POST['group'])?intval($_POST['group']):NULL;

      	// $_SESSION['groupO'] = $group;
      	// $_SESSION['post'] = $_POST;

      	$acadYear = isset($_POST['acadYear']) && !empty($_POST['acadYear'])?intval($_POST['acadYear']):die('Uknown Academic Year');
      	$sem = isset($_POST['sem']) && !empty($_POST['sem'])?intval($_POST['sem']):die('Uknown Semester');

      	// get Selected status list

      	$status = isset($_POST['statusList']) && !empty($_POST['statusList']) ? (array)json_decode($_POST['statusList']) : [];

      	// $recordSrd2 = ;

      	return $this->render('_report',['model' => $this->getModelRecords($group,$status, $adm,$prog,$dept,$clslvl,$spec,$acadYear,$sem),'statusList' => $status]);


    }

    public function actionGetReport(){
    	$adm = isset($_POST['adm']) && !empty($_POST['adm'])?intval($_POST['adm']):die('Please select Admission');
      	$prog = isset($_POST['prog']) && !empty($_POST['prog'])?intval($_POST['prog']):die('Please select Program');
      	$dept = isset($_POST['dept']) && !empty($_POST['dept'])?intval($_POST['dept']):NULL;
      	$clslvl = isset($_POST['clslvl']) && !empty($_POST['clslvl'])?intval($_POST['clslvl']):NULL;
      	$spec = isset($_POST['spec']) && !empty($_POST['spec'])?intval($_POST['spec']):NULL;
      	$group = isset($_POST['group']) && !empty($_POST['group'])?intval($_POST['group']):NULL;

      	// $_SESSION['groupO'] = $group;
      	$_SESSION['post'] = $_POST;

      	$acadYear = isset($_POST['acadYear']) && !empty($_POST['acadYear'])?intval($_POST['acadYear']):die('Uknown Academic Year');
      	$sem = isset($_POST['sem']) && !empty($_POST['sem'])?intval($_POST['sem']):die('Uknown Semester');

      	// get Selected status list

      	$status = isset($_POST['statusList']) && !empty($_POST['statusList']) ? (array)json_decode($_POST['statusList']) : [];

      	// $recordSrd2 = ;

      	return $this->renderPartial('_report',['model' => $this->getModelRecords($group,$status, $adm,$prog,$dept,$clslvl,$spec,$acadYear,$sem),'statusList' => $status]);


    }
    public function actionIndex()
    {
        $modelSssh = new \backend\models\StudentSemesterStatusHistory();
        return $this->render('index',['model' => $modelSssh]);
    }

    private function getModelRecords($group,$status, $admId,$progId,$deptId,$clslvlId,$spec,$acadYear,$semId){
    	$model = new \backend\models\SemesterRegistration2Detail();

    	$Gsets = [];//Holds set of Gset IDs that are under <selection>
    	$modelGset = new \backend\models\WGroupSetup();
    	if (isset($group) && !is_null($group)) {
    		// things will be simplified
    		$Gsets[] = $group;
    	}else{
    		$GsetObjects = $modelGset->getGsets($admId,$progId,$deptId,$clslvlId,$acadYear,$semId,$spec);

    		if ($GsetObjects && count($GsetObjects) > 0) {
    			foreach ($GsetObjects as $keyGset) {
    				$Gsets[] = $keyGset->id;
    			}
    		}
    	}

    	if (count($Gsets) > 0) {
    		return $model->find()->where(['srd2_group_relId' => $Gsets])->all();
    	}else{
    		return "Selection Error, Please correct and try again";
    	}

    }

}
