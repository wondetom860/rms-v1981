
<?php
use yii\helpers\Url;
$this->title = "Student Profile";

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-9">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 style="text-align: center;">Student Profile Information</h3>
				</div>
				<div class="panel-body">
					<div class="row" style="background-color: #79D;">
						<div class="col-md-8">
							<?php

							$user = \backend\models\User::findOne(['id'=>Yii::$app->user->identity->id]);

								if (Yii::$app->user->can('Student')) {
									$student = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

									if (!$student || is_null($student)) {
										// load admission form: 
										echo "Student profile information not completed yet.";
										return Yii::$app->response->redirect(Url::to(['/admit-student/new-admission'], true));

									}

									$spec = isset($student->std_spec_relId)?\backend\models\Specialization::findOne(['id'=>$student->std_spec_relId])->spec_name:'-';
									// $semId = \backend\models\Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;

									$gset = \common\components\CommonControllerFunctions::currentStudentGroup($student);
									// $gset = $student->stdGroupReal;

									$group = isset($gset)?\backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name:'-';
									$acdY = isset($gset)?''.$gset->gsAcadYearRel->sch_year_desc .' - '. $gset->gsSemRel->semester_name:'-';
									$cc = isset($gset)?\backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num:(is_null($student->std_clslvl_relId)?'-':\backend\models\ClassLevel::findOne(['id'=>$student->std_clslvl_relId])->clslvl_num);
									$cc = is_numeric($cc)?($cc == 1?'1<sup>st</sup>':($cc == 2?'2<sup>nd</sup>':($cc == 3?'3<sup>rd</sup>':$cc.'<sup>th</sup>'))):($cc);
									$cc = $cc == '-'?'-':$cc.'Year.';
									$mm = isset($gset)?'':'You are not assigned to any group in this semester. Please contact registrar clerk if this is error';

									$semStat = 'Active';
									$semStat =  is_null($student->std_semester_status)?$semStat:$student->std_semester_status;

									$dddd = is_null($student->std_dept_relId)?'<span style="color:red;"><i>Not Assigned Yet.</i></span>':\backend\models\Department::findOne(['id'=>$student->std_dept_relId])->dept_name;

									$ssss = " -- ";

									$ssss = is_null($student->std_schlrshpType_relId)?'':\backend\models\ScholarshipType::findOne($student->std_schlrshpType_relId)->schType_name;
									// $btnEtnGroup = $user->ethGroupRel ? "{$user->ethGroupRel->name}({$user->ethGroupRel->regionRel->region_name})" : "<button class='btn btn-xs btn-primary pull-center' onclick='loadEthGroupForm($student->id); return false;' title='Enter Your Ethnic Group'>Enter Here</button>";
									// <br><b>Ethnic Group : </b><u>{$btnEtnGroup}</u>
									echo "	
											<br><b>Reg. ID: : </b><u>{$student->std_regId}</u>
											<br><b>Full Name : </b><u>".$user->getFullName() ."</u>
											<br><b>Gender: </b><u>{$user->gender}</u>
											<br><b>Program: </b><u>". \backend\models\Program::findOne(['id'=>$student->std_prog_relId])->program_name ." ({$student->stdAdmissionRel->admission_name})</u>
											<br><b>Sponsorship: </b><u>{$ssss}</u>
											<br><b>Class Level: </b><u>". $cc ."</u>
											<br><b>Department: </b><u>". $dddd ."</u>
											<br><b>Specialization: </b><u>". $spec ."</u>
											<br><b>Group: </b><u>". $group ."</u> [ </b><u>{$acdY}</u> ]
											<br><b>Semester Status: </b><u>". $semStat ."</u>
											<br><b class='bg-warning' style='font-size:8pt;'><i>{$mm}</i></b>

									";
								}

								?>
						</div>
						<div class="col-md-4" id="profPic">
							<div class="col-md-12"  style="vertical-align: middle; text-align: center; padding: 10px 5px;"><br><br>
								<?php echo '<img height="160" width="120" src="/uploads'.'/'.$user->photo_num.'">'
							?>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					
				</div>
			</div>
		</div>
	</div>
</div>
<!-- profPic -->
<script type="text/javascript">
	window.loadEthGroupForm = (stdId) => {
		$('#modal').find('.modal-header').html('Enter your ethnic group');
        $('#modalContent').css('text-align','left');
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-md');
        $('#modal').modal('show');
        $.get('/ethnic-group/get-list',{stdId:stdId}).done(function(data){
            $('#modalContent').html(data);
        });
	}
</script>
<?php
	$this->registerJs(
			"
			window.loadProfileImg = function(){
				$.post('/my-profile/load-profile-image',{},function(data){
						$('#profPic').html(data);
					});
			}

			//loadProfileImg();
			",
			$this::POS_READY);

?>