<?php

namespace frontend\controllers;
use yii;
use yii\base\ErrorException;

use \common\components\CommonControllerFunctions as CCF;

class ApproveGroupAssignmentController extends \yii\web\Controller
{

	public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    // public function actionIndex()
    // {
    //     return $this->render('index');
    // }

    public function actionIndex()
    {
    	if (Yii::$app->user->can('Registrar')) {
    		return $this->render('index_registrar');
    	}
        return $this->render('index');
    }

    protected function drawTable($reqs, $app=NULL){

            echo "<table class='table table-striped table-condensed table-xs table-bordered table-hover' style='font-size:10pt;'>";
            echo "<caption><p class='bg-success'> <i class='bg-success'>". count($reqs) ."</i> Group Assignments made in this semester.</p></caption>";
            echo "<thead style='background-color:#ace;'>
            <th>#</th>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Department</th>
            <th>Class Level</th>
            <th>Prev. Group</th>
            <th>This Sem. Grp</th>";
            if ($app == 1) {
	            echo "
            	<th>Option</th>";
	        }else{
	            	
	            echo "
            	<th>Status</th>";
	        }
           
            echo "
            </thead><tbody>";
            $count = 0;
	        foreach ($reqs as $std) {
	            $student = \backend\models\Student::findOne(['id'=>$std['gas_std_relId']]);
	            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
	            $rap = is_null($std['gas_approved'])?'Pending':($std['gas_approved'] == 1?'Approved':'Rejected');

	            $afggset = \backend\models\WGroupSetup::findOne(['id'=>$std['gas_gset_relId']]);
	            $prevgrpgset = is_null($std['gas_prevGroup_relId'])?'':\backend\models\WGroupSetup::findOne(['id'=>$std['gas_prevGroup_relId']])->gs_group_relId;

	            $dept = \backend\models\Department::findOne(['id'=>$afggset->gs_dept_relId])->dept_name;
	            $clslvl = \backend\models\ClassLevel::findOne(['id'=>$afggset->gs_clslvl_relId])->clslvl_num;

	            $grpName1 = is_null($std['gas_prevGroup_relId'])?'-':\backend\models\WGroup::findOne(['id'=>$prevgrpgset])->group_name;
	            $grpName2 = \backend\models\WGroup::findOne(['id'=>$afggset->gs_group_relId])->group_name;
	            // <td>
	            echo "<tr>
	                <td>". ++$count ."</td>
	                <td>". $student->std_regId ."</td>
	                <td>". $usr->first_name.' '. $usr->middle_name ."</td>
	                <td>{$dept}</td>
	                <td style='text-align:center;'>{$clslvl}</td>
	                <td style='text-align:center;'>{$grpName1}</td>
	                <td style='text-align:center;'>{$grpName2}</td>";

	            if ($app == 1) {
	            	echo "
		                <td style='text-align:center;'>
		                	<input type='checkBox' onchange='addToList({$student->id})' />
		                </td>";	
	            }else{
	            	$Status = is_null($std['gas_approved'])?'Pending':($std['gas_approved'] == 1?'Approved':'Rejected');
	            	echo "
		                <td style='text-align:center;'>
		                	{$Status}
		                </td>";
	            }
	          
	        }
        echo "</tbody></table>";
    }

    public function actionGetApprovedAssignments(){
        $this->__constructor();
        $reqs = NULL;
        // ->andWhere(['IS NOT','gas_approved',NULL])
        if (Yii::$app->user->can('Registrar')) {
           $reqs = \backend\models\WGroupAssignment::find()
           			->innerJoin('w_group_setup','w_group_setup.id = w_group_assignment.gas_gset_relId')
                    ->where([
                        'gs_acadYear_relId'=>$this->acadYearId,
                    ])
                    ->andWhere(['IS NOT','gas_approved',NULL])
                    ->andWhere(['gs_sem_relId'=>$this->semId])
                    ->all();
        }
        
        if ($reqs) {
        	$this->drawTable($reqs,0);
        }else{
            echo "<p class='bg-danger'>No group assignment approved.</p>";
        }
    }

    public function actionGetAssignments(){
        $this->__constructor();
        $reqs = NULL;
        // 
        if (Yii::$app->user->can('Registrar')) {
           $reqs = \backend\models\WGroupAssignment::find()
           			->innerJoin('w_group_setup','w_group_setup.id = w_group_assignment.gas_gset_relId')
                    ->where([
                        'gs_acadYear_relId'=>$this->acadYearId,
                    ])
                    ->andWhere(['gas_approved'=>NULL])
                    ->andWhere(['gs_sem_relId'=>$this->semId])
                    ->all();
        }
        
        if ($reqs) {
        	$this->drawTable($reqs,1);
        }else{
            echo "<p class='bg-danger'>No group assignment approved.</p>";
        }
    }


}
