<?php

namespace frontend\controllers;
use yii;
use backend\models\Student1;
use common\models\User1;
use \common\components\CommonControllerFunctions as CCF;

class ApproveAdmissionController extends \yii\web\Controller
{
	public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    public function actionIndex()
    {
        //  || Yii::$app->user->can('Admin') || Yii::$app->user->can('Clerk')
    	if (Yii::$app->user->can('Registrar')) {
    		return $this->render('index_Registrar');
    	}
        return $this->render('index');
    }

    public function actionBackDept(){
    	if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin') || Yii::$app->user->can('Clerk')) {
    		$stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?intval($_POST['stdId']):die('Uknown Student');

    		$exists = \backend\models\Student1::find()
    			->where([
    				'id'=>$stdId,
    			])->andWhere(['IS NOT','std_dept_relId',NULL])
    			->One();

    		if ($exists && count($exists) > 0) {
    			$result = Yii::$app->db->createCommand()
    				->update('student',
    					['std_dept_relId'=>NULL,'std_clslvl_relId'=>NULL],
    					['id'=>$stdId]
    					)
    				->execute();
    			if ($result) {
    				echo 1;
    			}else{
    				echo "<p class='bg-danger'>Changing failed! Please re-try.</p>";die();
    			}
    		}else{
    			die("<p class='bg-danger'>Uknown Record!.</p>");
    		}
    	}
    }
    public function actionBackActivation(){

    	if (Yii::$app->user->can('Registrar')){
    		$stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?intval($_POST['stdId']):die('Uknown Student');
    		$exists = \backend\models\Student1::find()
    			->where([
    				'student.id'=>$stdId,
    			])->innerJoin('user','user.id = student.std_user_relId')
    			->andWhere(['IS NOT','activated',NULL])
    			->andWhere(['NOT','status = 2'])
    			->One();
    		if ($exists && count($exists) > 0) {
    			$result = Yii::$app->db->createCommand()
    				->update('user',
    					['status'=>2,'activated'=>NULL],
    					['id'=>$exists->std_user_relId]
    					)
    				->execute();
    			if ($result) {
    				echo 1;
    			}else{
    				echo "<p class='bg-danger'>Changing failed! Please re-try.</p>";die();
    			}
    		}else{
    			die("<p class='bg-danger'>Such record does NOT exists!.</p>");
    		}
    	}else{
    		die("<p class='bg-danger'>You have no privillege for this action.</p>");
    	}   	
    }

    public function actionApproveAdmissionReg(){
    	// std_admission_approved_at
    	// std_admission_approved_by

    	$stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?$_POST['stdId']:die('Student Not selected');
    	$valid = isset($_POST['valid']) && !empty($_POST['valid'])?$_POST['valid']:die('Status Uknown...');

    	if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
    		// update std info

    		$transaction = Yii::$app->db->beginTransaction();
    		$std = Student1::findOne($stdId);

    		$result = Yii::$app->db->createCommand()
    			->update('student',
    				[
    					'std_admission_approved_at'=>date('Y-m-d h:i:s'),
    					'std_admission_approved_by'=>Yii::$app->user->identity->id,
    				],
    				[
    					'id'=>$stdId,
    				])
    			->execute();
    		if ($result) {
    			// update user info whose id is std_user_relId

    			// get User Name and PassWord.
    			$model = new User1();
    			$un = $model->getUsername();
    			$pw = $model->getPassword();

    			$result2 = Yii::$app->db->createCommand()
    				->update('user',
    					[
    						"username"=>$un,
    						"status"=>10,
    						"activated"=>1,
                            'activated_at' => time(),
                            'activated_by' => Yii::$app->user->identity->id
    					],
    					[
    						'id'=>$std->std_user_relId
    					])
    				->execute();
    			if ($result2) {
    				// allow student role for the user.

    	// 			$auth = Yii::$app->authManager;
     //        		$role = $auth->getRole('Student');
					// $auth->assign($role,$std->std_user_relId);

    				echo json_encode([1,$un]);
    				$transaction->commit();
    				die();
    			}else{
    				$transaction->rollBack();
    				echo "<p class='bg-danger'>ERROR, Approving admission failed, INTERNAL_ERROR!</p>";die();
    			}
    		}else{
    			echo "<p class='bg-danger'>ERROR, Approving admission failed, INTERNAL_ERROR!</p>";die();
    		}
    	}else{
    		echo "<p class='bg-danger'>ERROR, INVALID_ACCESS!, You have no privilage for this action...</p>";die();
    	}
    }
    public function actionChangeDepartment(){
    	if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin') || Yii::$app->user->can('Clerk')) {
    		$stdList = isset($_POST['stdList']) && !empty($_POST['stdList'])?$_POST['stdList']:die('Student list Empity');
    		$deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?intval($_POST['deptId']):die('Selected department not known.');

    		$stdListArray = json_decode($stdList);

    		$transaction = Yii::$app->db->beginTransaction();
    		if (is_array($stdListArray)) {
    			$result = Yii::$app->db->createCommand()
    				->update('student',
    					[
    						'std_dept_relId'=>$deptId,
    					],
    					[
    						'id'=>$stdListArray,
    					])
    				->execute();
    			if ($result) {
    				$transaction->commit();
    				echo 1;
    				die();
    			}else{
    				$transaction->rollBack();
    				die("<p class='bg-danger'>Admission approval failed, INTERNAL_ERROR!!</p>");
    			}
    		}else{
    			die("<p class='bg-danger'>Student List Error!!</p>");
    		}
    	}
    }

    public function actionGetApprovedRecords(){
        if (Yii::$app->user->can('Registrar')) {
            $this->__constructor();
            // ->andWhere(['OR',
            //      ['std_dept_relId' => NULL],
            //      ['std_clslvl_relId' => NULL],
            //      ['status'=>2],
            //      ['activated'=>NULL]
            //  ])
            $stds = Student1::find()
                ->where([
                    'std_acadYear_relId' => $this->acadYearId,
                ])
                ->andWhere(['user.status'=>10,'user.activated'=>1])
                ->andWhere(['IS NOT','std_dept_relId',NULL])
                ->andWhere(['IS NOT','std_clslvl_relId',NULL])
                ->innerJoin('user','user.id = student.std_user_relId')
                ->with('stdUserRel')
                ->distinct('id')
                ->all();

            // echo "<pre>"; var_dump($stds);die();


            $table1 = "";
            $table2 = "";
            $yy = Yii::$app->params['C']['Y'];
            if ($stds && count($stds) > 0) {
                $table1 = "<table class='table table-sm table-bordered table-condensed' style='font-size:9pt;'>";
                $table1 .= "<caption>
                            <h4 class='bg-success' style='text-align:center; text-decoration:underline;'>New Admitted Students, for Academi Year of <i>{$yy}</i>.</h4>
                            </caption>";
                $table1 .= "<thead style='background-color:#333; color:white; vertical-align:middle;'>
                            <tr>
                                <th rowspan = 2>#</th>
                                <th rowspan = 2>Reg. ID</th>
                                <th rowspan = 2>Full Name</th>
                                <th rowspan = 2>Admission</th>
                                <th rowspan = 2>Program</th>
                                <th colspan = 3 style='text-align:center;'>Department Assigned</th>";
                          if (Yii::$app->user->can('Registrar')) {
                                 $table1 .= "<th rowspan = 2 style='text-align:center;'>Activated</th>";
                            }
                $table1 .= "
                            </tr>";
                $table1 .= "
                            </tr>
                        </thead><tbody>";
                $table2 = $table1;
                $count = 0;
                $optionsss = "<option value=''> Select Department </option>";
                $dept = \backend\models\Department::find()->all();
                if ($dept && count($dept) > 0) {
                    foreach ($dept as $keye) {
                        $optionsss .=  "<option value='{$keye->id}'>{$keye->dept_name}</option>";
                    }
                }
                $deptSelect = "<select class='form-control' id='selected_department'>{$optionsss}</select>";
                $btn = "<button class='btn btn-sm btn-success' onclick='setStudetntDept()' title='Assign selected studets to the department'>Assign</button>";
                $text = "Change Deaprtment of selected Students";

                foreach ($stds as $key) {
                    $user = User1::findOne($key->std_user_relId);
                    if ($user && count($user) > 0) {
                        # code...
                    }else{
                        continue;
                        // What this student is doing here?
                    }

                    $approved = false;
                    $deptAssigned = false;

                    // "<input type='checkbox' onchange='addEnroll(". $course->id .",". $coff2['id'] .")'>"
                    $chkBx = "";
                    if (is_null($key->std_dept_relId)) {
                        $chkBx = "<input type='checkbox' onchange='addToList({$key->id})' title='Assign student to selected department.'>";
                    }else{
                        $deptAssigned = true;
                        $dn = \backend\models\Department::findOne($key->std_dept_relId)->dept_name;
                        $chkBx = "<button class='btn btn-xs btn-warning' title='This Student was assigned to {$dn}. Click here if you want to change.' onclick='changeBack({$key->id},0)'>Change</button>";
                    }

                    $btnApprove = '';
                    if (is_null($key['stdUserRel']['activated']) || $key['stdUserRel']['status'] == 2) {
                        $btnApprove = "<button class='btn btn-xs btn-success pull-left' onclick='approveAdmission({$key->id},1)'>YES</button>&nbsp;<button class='btn btn-xs btn-danger pull-right' onclick='approveAdmission({$key->id},0)'>NO</button>";
                    }else{
                        $approved = true;
                        $btnApprove = "<button class='btn btn-xs btn-warning btn-block' title='Change Admission approval status, Now the account is active. Click here if you want to change it.' onclick='changeBack({$key->id},1)'>Change</button>";
                    }
                    

                    $adm = \backend\models\Admission::findOne($key->std_admission_relId);
                    $program = \backend\models\Program::findOne($key->std_prog_relId);

                    $deptChoice = is_null($key->std_deptChoice)?NULL:json_decode($key->std_deptChoice);

                    // var_dump( $deptChoice);die();
                    
                    $deptChoice1 = "<span style='color:red; font-style:italic;'>Not choosen</span>";
                    $deptChoice2 = "<span style='color:red; font-style:italic;'>Not choosen</span>";
                    $deptChoice3 = "<span style='color:red; font-style:italic;'>Not choosen</span>";

                    if (is_object($deptChoice)) {
                        foreach ($deptChoice as $key2 => $value) {

                            if ($key2 == 1 && $value != NULL) {
                                $deptChoice1 = \backend\models\Department::findOne($value)->dept_name;
                            }else if($key2 == 2 && $value != NULL){
                                $deptChoice2 = \backend\models\Department::findOne($value)->dept_name;
                            }else if ($key2 == 3 && $value != NULL) {
                                $deptChoice3 = \backend\models\Department::findOne($value)->dept_name;
                            }
                        }
                    }else if (is_array($deptChoice)) {

                        $count = 0;
                        foreach ($deptChoice as $dckey) {
                            if ($count == 0) {
                                $deptChoice1 = \backend\models\Department::findOne($dckey)->dept_name;
                            }else if ($count == 1) {
                                $deptChoice2 = \backend\models\Department::findOne($dckey)->dept_name;
                            }else if ($count == 2) {
                                $deptChoice3 = \backend\models\Department::findOne($dckey)->dept_name;
                            }else{
                                break;
                            }
                            $count++;
                        }
                    }

                    if ($approved && $deptAssigned) {
                        $table2 .= "<tr>
                            <td>".++$count."</td>
                            <td>{$key->std_regId}</td>
                            <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                            <td>{$adm->admission_name}</td>
                            <td>{$program->program_name}</td>
                            <td>{$dn}</td>
                            <td style='text-align:center;'>{$chkBx}</td>";
                            if (Yii::$app->user->can('Registrar')) {
                                 // $table2 .= "<td>{$btnApprove}</td>";
                            }
                    $table2 .= "</tr>";
                    }

                    // $ff = $approved && $deptAssigned?'YES':'NOT YET';
                    
                }
            $table2 .= "</tbody></table>";
            echo json_encode([$table2]);
            die();
            }else{
                echo json_encode(["<p class='bg-warning'>No records found.</p>"]);
            }
        }
    }
    public function actionGetRecords(){
        //  || Yii::$app->user->can('Admin') || Yii::$app->user->can('Clerk')
        // ->andWhere(['student.std_admission_approved_by'=>NULL])
    	if (Yii::$app->user->can('Registrar')) {
    		$this->__constructor();
    		$stds = Student1::find()
    			->where([
    				'std_acadYear_relId' => $this->acadYearId,
    			])
                ->andWhere(['user.status'=>10,'user.activated'=>1])
    			->innerJoin('user','user.id = student.std_user_relId')
                ->andWhere(['student.std_group_realId'=>NULL])
                ->andWhere(['OR',['student.std_dept_relId'=>NULL],['student.std_clslvl_relId'=>NULL]])
    			->with('stdUserRel')
    			->distinct('id')
    			->all();

    		$table1 = "";
    		$table2 = "";
            $optionsss = "<option value=''> Select Department </option>";
    		$yy = Yii::$app->params['C']['Y'];
    		if ($stds && count($stds) > 0) {
                $dept = \backend\models\Department::find()->all();
                if ($dept && count($dept) > 0) {
                    foreach ($dept as $keye) {
                        $optionsss .=  "<option value='{$keye->id}'>{$keye->dept_name}</option>";
                    }
                }
                $deptSelect = "<div class='col-md-10'>
                        <p>List of students admitted for this academic year. Select department and assign.</p>
                    </div><div class='col-md-2'><select class='form-group pull-right' id='selected_department'>{$optionsss}</select></div>";
    			$table1 = "<table class='table table-sm table-bordered table-condensed table-hover' style='font-size:9pt;'>";
	            $table1 .= "<caption>
	                        <h4 class='bg-default' style='text-align:center; text-decoration:underline;'>New Admitted Students, for Academi Year of <i>{$yy}</i>.</h4>
                            {$deptSelect}
	                        </caption>";
	            $table1 .= "<thead style='background-color:#333; color:white; vertical-align:middle;'>
	                        <tr>
		                        <th rowspan = 2>#</th>
		                        <th rowspan = 2>Reg. ID</th>
		                        <th rowspan = 2>Full Name</th>
		                        <th rowspan = 2>Admission</th>
		                        <th rowspan = 2>Program</th>
		                        <th colspan = 3 style='text-align:center;'>Department Choice</th>";
		                  if (Yii::$app->user->can('Registrar')) {
					        	 $table1 .= "<th rowspan = 2 style='text-align:center;'>Choose Dept.</th>";
					        }
		        $table1 .= "
	                        </tr>
	                        <tr>
		                        <th style='text-align:center;'>1<sup>st</sup> Choice</th>
		                        <th style='text-align:center;'>2<sup>nd</sup> Choice</th>
		                        <th style='text-align:center;'>3<sup>rd</sup> Choice</th>";
		        $table1 .= "
	                        </tr>
	                    </thead><tbody>";
	            $table2 = $table1;
	            $count = 0;
	            $text = "Change Deaprtment of selected Students";

    			foreach ($stds as $key) {
    				$user = User1::findOne($key->std_user_relId);
    				if ($user && count($user) > 0) {
    					# code...
    				}else{
    					continue;
                        die('ERROR');
    				}

    				$approved = false;
    				$deptAssigned = false;

    				// "<input type='checkbox' onchange='addEnroll(". $course->id .",". $coff2['id'] .")'>"
    				$chkBx = "";
    				if (is_null($key->std_dept_relId)) {
    					$chkBx = "<input type='checkbox' onchange='addToList({$key->id})' title='Assign student to selected department.'>";
    				}else{
    					$deptAssigned = true;
    					$dn = \backend\models\Department::findOne($key->std_dept_relId)->dept_name;
    					$chkBx = "<button class='btn btn-xs btn-warning' title='This Student was assigned to {$dn}. Click here if you want to change.' onclick='changeBack({$key->id},0)'>Change</button>";
    				}

    				$btnApprove = '';
    				if (is_null($key['stdUserRel']['activated']) || $key['stdUserRel']['status'] == 2) {
    					$btnApprove = "<button class='btn btn-xs btn-success pull-left' onclick='approveAdmission({$key->id},1)'>YES</button>&nbsp;<button class='btn btn-xs btn-danger pull-right' onclick='approveAdmission({$key->id},0)'>NO</button>";
    				}else{
    					$approved = true;
    					$btnApprove = "<button class='btn btn-xs btn-warning btn-block' title='Change Admission approval status, Now the account is active. Click here if you want to change it.' onclick='changeBack({$key->id},1)'>Change</button>";
    				}
    				

    				$adm = \backend\models\Admission::findOne($key->std_admission_relId);
    				$program = \backend\models\Program::findOne($key->std_prog_relId);

    				$deptChoice = is_null($key->std_deptChoice)?NULL:json_decode($key->std_deptChoice);

    				$deptChoice1 = "<span style='color:red; font-style:italic;'>Not choosen</span>";
    				$deptChoice2 = "<span style='color:red; font-style:italic;'>Not choosen</span>";
    				$deptChoice3 = "<span style='color:red; font-style:italic;'>Not choosen</span>";

    				if (is_object($deptChoice)) {
                        foreach ($deptChoice as $key2 => $value) {

                            if ($key2 == 1 && $value != NULL) {
                                $deptChoice1 = \backend\models\Department::findOne($value)->dept_name;
                            }else if($key2 == 2 && $value != NULL){
                                $deptChoice2 = \backend\models\Department::findOne($value)->dept_name;
                            }else if ($key2 == 3 && $value != NULL) {
                                $deptChoice3 = \backend\models\Department::findOne($value)->dept_name;
                            }
                        }
                    }else if (is_array($deptChoice)) {

                        $count = 0;
                        foreach ($deptChoice as $dckey) {
                            if ($count == 0) {
                                $deptChoice1 = \backend\models\Department::findOne($dckey)->dept_name;
                            }else if ($count == 1) {
                                $deptChoice2 = \backend\models\Department::findOne($dckey)->dept_name;
                            }else if ($count == 2) {
                                $deptChoice3 = \backend\models\Department::findOne($dckey)->dept_name;
                            }else{
                                break;
                            }
                            $count++;
                        }
                    }

                    $btnMore = "<button class='btn btn-primary btn-xs btn-flat' onclick='loadApplicationForm({$user->id})'>Show</button>";
					$table1 .= "<tr>
						<td>".++$count."</td>
						<td>{$key->std_regId}</td>
						<td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
						<td>{$adm->admission_name}</td>
						<td>{$program->program_name}</td>
						<td>{$deptChoice1}</td>
						<td>{$deptChoice2}</td>
						<td>{$deptChoice3}</td>
						<td style='text-align:center;'>{$chkBx}</td>";
						if (Yii::$app->user->can('Registrar')) {
				        	 // $table1 .= "<td>{$btnApprove}</td>";
				        }
					$table1 .= "</tr>";
    				// }

    				// $ff = $approved && $deptAssigned?'YES':'NOT YET';
    				
    			}

    		$table1 .= "</tbody></table>";
    		$table2 .= "</tbody></table>";
    		echo json_encode([$table1,$table2]);
    		die();
    		}else{
    			echo json_encode(["<p class='bg-warning'>No records found.</p>"]);
    		}
    	}
    }

}
