<?php

namespace frontend\controllers;
use yii;
use common\models\User1;
use backend\models\Instructor;
use backend\models\Student;
use yii\filters\VerbFilter;

class ApproveUserSignupController extends \yii\web\Controller
{
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
               ]
           ]
        ];
    }

    public function actionIndex(){
    	if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')|| Yii::$app->user->can('UniversityRegistrar')) {
    		return $this->render('index_Registrar');
    	}
        return $this->render('index');
    }

    public function actionApproveUserSignupAccount(){
    	if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
    		$userId = isset($_POST['userId']) && !empty($_POST['userId'])? intval($_POST['userId']) : die('Uknown user ID');
    		$valid = isset($_POST['valid']) && !empty($_POST['valid'])? intval($_POST['valid']) : die('Uknown Status Change Entry.');

    		$transaction = Yii::$app->db->beginTransaction();
			// update user info whose id is std_user_relId

    		$user = User1::findOne($userId);
    		if ($user) {
    			$user->getPassword();
    			$user->username = $user->getUsername();
    			$user->activated_at = time();
    			$user->activated_by = Yii::$app->user->identity->id;
    			$user->status = 10;
    			$user->activated = 1;

    			if ($user->update(false)) {
					echo json_encode([1,$user->username]);
					$transaction->commit();
					die();
				}else{
					$transaction->rollBack();
					var_dump($user->getErrors());
					echo "<p class='bg-danger'>ERROR, Approving Account failed, INTERNAL_ERROR!</p>";die();
				}
    		}
			// // get User Name and PassWord.
			// $model = new User1();
			// $un = $model->getUsername();
			// $pw = $model->getPassword();

			// $result2 = Yii::$app->db->createCommand()
			// 	->update('user',
			// 		[
			// 			"username"=>$un,
			// 			"status"=>10,
			// 			"activated"=>1,
			// 			"activated_at" => time(),
			// 			"activated_by" => Yii::$app->user->identity->id,
			// 		],
			// 		[
			// 			'id'=>$userId
			// 		])
			// 	->execute();
			
    	}
    }

    public function actionListAll(){
    	if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')|| Yii::$app->user->can('UniversityRegistrar')) {
    		$users = User1::find()
    			->where(['OR',['user.status'=>2],['user.activated'=>NULL]])
                ->orderBy('user.first_name')
    			->all();
    		if ($users && count($users) > 0) {
    			$tableStudent = "";
    			$tableInst = "";
    			$tableOthers = "";

    			$tableStudent = $tableInst = $tableOthers = "<div class='container-fluid' style=' height: 400px; position: relative;  width: 100%; overflow-y: auto;'>
    					<table class='table table-hover table-sm table-bordered ' border='1'> ";
    			$tableStudent .= " <caption style='text-align: center; background-color:#333; color:white;'>
    					Student signup requests
    					</caption> 
    			<thead style='background-color:#8D0;'>
    				<th>#</th>
    				<th>Reg. ID</th>
    				<th>Full Name</th>
                    <th>Gender</th>
                    <th>Pl. of Brth.</th>
                    <th>Nationality</th>
    				<th>Admission</th>
    				<th>Program</th>
    				<th>Acct. Crtd. By</th>
    				
    				<th>OTP</th>
    				<th>Role</th>
    				<th>Status</th>
                    <th>Admission Form</th>
    				<th>Option</th>
    			</thead><tbody>";
    		$tableInst .= " <caption style='text-align: center; background-color:#333; color:white;'>
    					Instructor signup requests
    					</caption> 
    			<thead style='background-color:#8D0;'>
    				<th>#</th>
    				<th>Full Name</th>
    				<th>Department</th>
    				<th>Acct. Created By</th>
    				
    				<th>OTP</th>
    				<th>Role</th>
    				<th>Status</th>
    				<th>Option</th>
    			</thead><tbody>";
    		$tableOthers .= " <caption style='text-align: center; background-color:#333; color:white;'>
    					Other accounts' signup requests
    					</caption> 
    			<thead style='background-color:#8D0;'>
    				<th>#</th>
    				<th>Full Name</th>
    				<th>Acct. Created By</th>
    				
    				<th>OTP</th>
    				<th>Role</th>
    				<th>Status</th>
    				<th>Option</th>
    			</thead><tbody>";
    			$countI = $countS = $countO = 0;
    		foreach ($users as $key) {
    			$role = Yii::$app->authManager->getRolesByUser($key->id);
    			$role = is_null($role)?NULL:$role;
    			if (is_array($role)) {
    				if (empty($role)) {
    					$role = NULL;
    				}else{
    					// print_r(array_shift($role)->name);continue;
    					$role = array_shift($role)->name;
    					// $role = isset($role['Student'])?$role['Student']->name:(isset($role['Instructor'])?$role['Instructor']->name:"Another");
    				}
    			}

    			if (!is_null($role) && strtoupper($role) == 'STUDENT' && !Yii::$app->user->can('UniversityRegistrar')) {
    				continue;
    				// student account should be approved by University registrar
    			}else if(!is_null($role) && strtoupper($role) != 'STUDENT' && Yii::$app->user->can('UniversityRegistrar')){
    				continue;
    			}

	    		$fname = $key->first_name .' '.$key->middle_name .' '.$key->last_name;
	    		$status = $key->status == 2?'Not approved':($key->status== 0?'Deleted':($key->status == 10?'Activated':'Uknown'));
	    		$createdAt = $key->created_at;//eate($key->created_at), 'D, m d.');
                // $user = is_null($key->created_by)?'-':\common\models\User1::findOne($key->created_by);
                $createdBy = '';
                if (is_null($key->created_by) || $key->created_by == 0) {
                    $createdBy = 'Self';
                }else{
                    $cUser = User1::findOne($key->created_by);
                    if ($cUser ) {
                        $createdBy = $cUser->first_name .' '.$cUser->middle_name[0];
                    }
                }
	    		// $createdBy = is_null($key->created_by)?'Self':'Admin';

	    		$isStd = Student::find()->where(['std_user_relId'=>$key->id])->one();
	    		$isInst = Instructor::find()->where(['inst_user_relId'=>$key->id])->one();

    			if ($isStd && count($isStd) > 0) {
    				// Student

                    $nat = is_null($key->nationality)?'-':\backend\models\Nationality::findOne($key->nationality)->name;
                    // $plOfBrth = $key->place_of_birth_address_relId?$key->placeOfBirthAddressRel->addressRegionRel->region_name:'-';
                    $plOfBrth = $key->place_of_birth_address_relId?($key->placeOfBirthAddressRel->addressRegionRel?$key->placeOfBirthAddressRel->addressRegionRel->region_name:'-'):'-';
    				$student = Student::findOne($isStd->id);
	    			$adm = is_null($student->std_admission_relId)?'-':\backend\models\Admission::findOne($student->std_admission_relId)->admission_name;
	    			$prog = is_null($student->std_prog_relId)?'-':\backend\models\Program::findone($student->std_prog_relId)->program_name;
	    			$btnApprove = "<button class='btn btn-xs btn-success btn-block' onclick='approveAdmission({$student->id},1)'>ACTIVATE</button>";
                    $btnMore = "<button class='btn btn-primary btn-xs btn-flat' onclick='loadApplicationForm({$key->id})'>Show</button>";
	    			$tableStudent .= "<tr>
    					<td>".++$countS."</td>
    					<td>{$student->std_regId}</td>
    					<td>{$fname}</td>
                        <td>{$key->gender}</td>
                        <td>{$plOfBrth}</td>
                        <td>{$nat}</td>
    					<td>{$adm}</td>
    					<td>{$prog}</td>
    					<td>{$createdBy}</td>
    					
    					<td style='font-size:7pt;'>{$key->OTP}</td>
    					<td>{$role}</td>
    					<td>{$status}</td>
                        <td>{$btnMore}</td>
    					<td>{$btnApprove}</td>
    				</tr>";

    			}else if($isInst){
    				// Instructor
    				$role = is_null($role)?'Instructor':$role;
    				$btnApprove = "<button class='btn btn-xs btn-success btn-block' onclick='approveSignUp({$key->id},1)'>ACTIVATE</button>";
    				$dept = \backend\models\Department::findOne($isInst->inst_dep_relId);
    				$department = empty($dept)?'-':$dept->dept_name;
    				$tableInst .= "<tr>
    					<td>".++$countI."</td>
    					<td>{$fname}</td>
    					<td>{$department}</td>
    					<td>{$createdBy}</td>
    					
    					<td>{$key->OTP}</td>
    					<td>{$role}</td>
    					<td>{$status}</td>
    					<td>{$btnApprove}</td>
    				</tr>";
    			}else{
    				// |Other
    				$role = is_null($role)?'Uknown':$role;
    				$btnApprove = "<button class='btn btn-xs btn-success btn-block' onclick='approveSignUp({$key->id},1)'>ACTIVATE</button>";
    				$tableOthers .= "<tr>
    					<td>".++$countO."</td>
    					<td>{$fname}</td>
    					<td>{$createdBy}</td>
    					
    					<td>{$key->OTP}</td>
    					<td>{$role}</td>
    					<td>{$status}</td>
    					<td>{$btnApprove}</td>
    				</tr>";
    			}
    		}

    		$tableStudent .= "</tbody></table>";
    		$tableInst .= "</tbody></table>";
    		$tableOthers .= "</tbody></table>";

    		echo json_encode([$tableStudent,$tableInst,$tableOthers,$countS,$countI,$countO]);
    		die();
    		}else{
    			echo "<p class='bg-danger'>No approval-pending student account found.</p>";die();
    		}
    	}
    }

    public function actionStdList(){

    	if (Yii::$app->user->can('Registrar')) {
    		// List un-approved student account
    		$stds = Student::find()
    			->innerJoin('user','user.id = student.std_user_relId')
    			->where(['OR',['user.status'=>2],['user.activated'=>NULL]])
    			->with('stdUserRel')
                ->orderBy('user.first_name')
    			->all();
    		if ($stds && count($stds) > 0) {
    			$table = "<div class='container-fluid' style=' height: 500px; position: relative;  width: 100%; overflow-y: scroll;'>
    					<table class='table table-hover table-sm table-bordered ' border='1'> ";
    			$table .= " <caption style='text-align: center;'>
    					Student signup requests
    					</caption> 
    			<thead style='background-color:#8D0;'>
    				<th>#</th>
    				<th>Reg. ID</th>
    				<th>Full Name</th>
    				<th>Admission</th>
    				<th>Program</th>
    				<th>OTP</th>
    				<th>Status</th>
    				<th>Option</th>
    			</thead><tbody style=''>";
    		$count = 0;
    		foreach ($stds as $key) {
    			$btnApprove = "<button class='btn btn-xs btn-success' onclick='approveAdmission({$key->id},1)'>YES</button>&nbsp; | &nbsp;<button class='btn btn-xs btn-danger' onclick='approveAdmission({$key->id},0)'>NO</button>";
    			$fname = $key['stdUserRel']['first_name'] .' '.$key['stdUserRel']['middle_name'] .' '.$key['stdUserRel']['middle_name'];
    			$adm = is_null($key->std_admission_relId)?'-':\backend\models\Admission::findOne($key->std_admission_relId)->admission_name;
    			$prog = is_null($key->std_prog_relId)?'-':\backend\models\Program::findone($key->std_prog_relId)->program_name;
    			$status = $key['stdUserRel']['status'] == 2?'Un-approved':($key['stdUserRel']['status'] == 0?'Deleted':($key['stdUserRel']['status'] == 10?'activated':'Uknown'));
    			$table .= "<tr>
    					<td>".++$count."</td>
    					<td>{$key->std_regId}</td>
    					<td>{$fname}</td>
    					<td>{$adm}</td>
    					<td>{$prog}</td>
    					<td>{$key['stdUserRel']['OTP']}</td>
    					<td>{$status}</td>
    					<td>{$btnApprove}</td>
    				</tr>";
    		}

    		$table .= "</tbody></table></div>";
    		echo "{$table}";
    		die();
    		}else{
    			echo "<p class='bg-danger'>No approval-pending student account found.</p>";die();
    		}
    	}else{
    		echo "<p class='bg-danger'>No privilege for this.</p>";die();
    	}
    }

    

}
