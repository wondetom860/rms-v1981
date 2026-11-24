<?php

namespace frontend\controllers;
use yii;
use \backend\models\WStudentReadmissionHistory;
use \backend\models\WStudentWithdrawalHistory;
use \backend\models\WGroupAssignment;
use \common\components\CommonControllerFunctions as CCF;

class RequestReadmissionController extends \yii\web\Controller
{

	public $acadYearId;
    public $semId;
    public $gset;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }


    public function actionIndex()
    {
    	if (\Yii::$app->user->can('Student')) {
            $swhObj = new \backend\models\WStudentWithdrawalHistory();
            $stdObj = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
            $modelSWH = $swhObj->hasValidWithdrawal($stdObj);

            if ($modelSWH && count($modelSWH) > 0) {
                $this->__constructor();
                $semObj = \backend\models\Semester::findOne($this->semId);
                $yearObj = \backend\models\SchoolYear::findOne($this->acadYearId);

                $retrunBackSchedule = $modelSWH->readmitScheduleCheck($semObj,$yearObj,$modelSWH);
                if (is_bool($retrunBackSchedule) && $retrunBackSchedule == true) {
                    Yii::$app->session->setFlash('type', "info");
                    Yii::$app->session->setFlash('message', "You can submit readmission request for this academic year.");
                    return $this->render('index',[
                        'model'=>new WStudentReadmissionHistory(),
                        'modelSWH'=>$modelSWH,
                    ]);
                }else{
                   Yii::$app->session->setFlash('message', "{$retrunBackSchedule}");
                   Yii::$app->session->setFlash('type', "error");
                    return $this->render('index'); 
                }
                // 
            }else{
                Yii::$app->session->setFlash('message', "You can NOT request readmission");
                Yii::$app->session->setFlash('type', "error");
                return $this->render('index');
            }
            // 
    	}else if (\Yii::$app->user->can('Registrar') || \Yii::$app->user->can('DepartmentHead')) {
    		return $this->render('index_approve');
    	}
        
    }

    public function actionAssignGroup(){
    	$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?intval($_POST['gsetId']):die('Inavlid query, No group entry selected.');
    	$stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?intval($_POST['stdId']):die('Inavlid query, Student info Uknown.');

    	$this->__constructor();
        $transaction = \Yii::$app->db->beginTransaction();

    	$gset = \backend\models\WGroupSetup::findOne(
    		[
    			'id'=>$gsetId,
    			'gs_acadYear_relId'=>$this->acadYearId,
    			'gs_sem_relId'=>$this->semId
    		]);

    	$std  = \backend\models\Student::findOne($stdId);

    	if ($gset->gs_clslvl_relId != $std->std_clslvl_relId) {
    		echo "<p class='bg-warning'>Invalid Class level selected.</p>";die();
    	}else{

    		// check if such exists before.
    		$exists = WGroupAssignment::findOne(['gas_gset_relId'=>$gset->id,'gas_std_relId'=>$std->id]);
    		if ($exists) {
    			// update.
    			$result = Yii::$app->db->createCommand()
    				->update('student',
                        [
                            'std_group_realId'=>$gset->id,
                            'std_semester_status'=>'ACTIVE'
                        ],['id'=>$std->id])
    				->execute();
    			if ($result) {
    				$transaction->commit();
    				echo 1; 
    				die();
    			}else{
    				$transaction->rollBack();
    				echo "<p class='bg-warning'>Error occured while assigning student to a group.</p>";die();
    			}
    		}else{
    			$model = new WGroupAssignment();
	    		$model->gas_std_relId = $std->id;
	    		$model->gas_gset_relId = $gset->id;
	    		$model->gas_created_at = date('Y-m-d h:i:s');
	    		if ($model->save()) {
	    			// update student profile with
	    			$result = \Yii::$app->db->createCommand()
	    				->update('student',['std_group_realId'=>$gset->id,'std_semester_status'=>'ACTIVE'],['id'=>$std->id])
	    				->execute();
	    			if ($result) {
	    				$transaction->commit();
	    				echo 1; 
	    				die();
	    			}else{
	    				$transaction->rollBack();
	    				echo "<p class='bg-warning'>Error occured while assigning student to a group.</p>";die();
	    			}
	    		}else{
	    			echo "<p class='bg-warning'>Studebt - group assignment failed.</p>";die();
	    		}
    		}
    	}
    }

    public function actionGetUngroupedStudents(){
        if (\Yii::$app->user->can('DepartmentHead')) {
            $instdept = \backend\models\Instructor::find()
                    ->where(['inst_user_relId'=>\Yii::$app->user->identity->id])
                    ->one()->inst_dep_relId;

            $this->__constructor();
            // var_dump($instdept);

            // die();
            $requests = \backend\models\WStudentReadmissionHistory::find()
                
                ->innerJoin('student','student.id = w_student_readmission_history.srh_std_relId')
                ->innerJoin('w_student_withdrawal_history','w_student_withdrawal_history.id = w_student_readmission_history.srh_swh_relId')
                ->where([
                    'srh_approved'=>1,
                    'srh_HOD_approved'=>1,
                ])
                ->andWhere(['<>','w_student_withdrawal_history.swh_returnedBack',1])
                ->andWhere(['student.std_dept_relId' => $instdept])
                ->all();

            if ($requests && count($requests) > 0) {
                $table = "<table class='table table-condensed table-hover table-striped table-bordered' style='font-size:9pt;'> ";
                $table .= "<thead style='background-color:#8D0'>
                            <th>#</th>
                            <th>Reg. ID</th>
                            <th>Full Name</th>
                            <th>Class Level</th>
                            <th>Spec.</th>
                            <th>Assign To Group</th>
                        </thead><tbody>";
                $count = 0;
                foreach ($requests as $key) {
                    $std = \backend\models\Student::findOne($key->srh_std_relId);
                    $user = \common\models\User1::findone($std->std_user_relId);
                    $clslvl = \backend\models\ClassLevel::findone($std->std_clslvl_relId);
                    $spec = is_null($std->std_spec_relId)?'-':\backend\models\Specialization::findOne($std->std_spec_relId)->spec_name;
                    $gsetEntry = ['acadYearId'=>$this->acadYearId,'semId'=>$this->semId,'deptId'=>$instdept, 'admId'=>$std->std_admission_relId,'progId'=>$std->std_prog_relId,'clslvlId'=>$std->std_clslvl_relId,'specId'=>$std->std_spec_relId];
                    $select = "<select class='form-control dept-group-select' onchange='AssignGroup($(this).val(),{$std->id})'></select>";
                    $table .= "<tr>
                                <td style='text-align:center;'>".++$count."</td>
                                <td>{$std->std_regId}</td>
                                <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                                <td style='text-align:center;'>{$clslvl->clslvl_num}</td>
                                <td>{$spec}</td>
                                <td>{$select}</td>
                            </tr>";
                }

                $table .= "</tbody></table>";
                echo json_encode([1,$table,$gsetEntry]);
                die(); 
            }else{
                echo "<p class='bg-warning'>No Ungrouped student found.</p>";die();
            }
        }
    }

    public function actionGetApplicationForm(){
    	if (\Yii::$app->user->can('Student')) {
    		$std = \backend\models\Student::find()->where(['std_user_relId'=>\Yii::$app->user->identity->id])->one();

            // echo "505"; die();
    		// check if active student, becaause active students cannot request re-admission.



    		// check if appied for withdrawal before.
            // 
    		$exists = WStudentWithdrawalHistory::find()
    			->where([
    				'swh_std_relId'=>$std->id,
    			])
                ->andWhere([
                	'swh_approved'=>1,
                    'swh_HOD_approved'=>1,
                    'swh_lastApproved'=>1,
                    'swh_stdSigned'=>1,
                    'swh_returnedBack'=>NULL,
                ])
    			->one();
    		if ($exists && count($exists) > 0) {

                // check if there exists the request for re-admission 

                $readExists = WStudentReadmissionHistory::find()
                    ->where(['OR',
                        ['srh_approved'=>NULL],['srh_HOD_approved'=>NULL]
                    ])
                    ->andWhere(['OR',
                        ['<>','srh_approved',0],['<>','srh_HOD_approved',0]])
                    ->andWhere(['srh_std_relId'=>$std->id])
                    ->all();

                if ($readExists && count($readExists) > 0) {
                    echo "<p class='bg-warning'>You have approval pending re-admission request.</p>";die();
                }
    			echo json_encode([1,$exists->id]);
    			die();
    		}else{
    			echo "<p class='bg-warning'>You DO NOT have approved withdrawal request made before OR your readmission request is under-proccess.</p>";die();
    		}

    		// $gset = CCF::currentStudentGroup($std);

    		// if ($gset && count($gset) > 0 ) {

    		// 	echo 1;
    		// 	// echo "<p class='bg-success'>You can request withdrawal</p>";die();
    		// }else{
    		// 	echo "<p class='bg-warning'> You are NOT assigned to any group in this semestr.</p>";die();
    		// }
    	}else if(\Yii::$app->user->can('DepartmentHead')){
    		// return $this->redirect('request-hod');
    		echo "Welcome";die();
    	}
    }

}
