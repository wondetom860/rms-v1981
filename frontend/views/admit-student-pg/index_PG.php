<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


use backend\models\Student1;
use common\models\User1;
use backend\models\EmergencyContact;
use backend\models\AddressInfo;
use backend\models\PgPostSecondarySchool;
use backend\models\PgEducationInfo;
use backend\models\PgSecondarySchoolsAttended;

$this->title = 'Admission Page - PG';
$this->params['breadcrumbs'][] = $this->title;

$modelUser = new User1();
$modelStudent = new Student1();
$emergencyContact = new EmergencyContact();
$modelAddress = new AddressInfo();
$modelPSE = [new PgPostSecondarySchool()];
$modelEI = new PgEducationInfo();
$modelSSA = [new PgSecondarySchoolsAttended()];
?><br>

<style type="text/css">
	.form-group.required .control-label:after {content:"*";color:red;font-weight: bold;}
	
</style>
<div class="container-fluid">

	<div class="panel panel-info">
		<div class="panel-heading">
			<h5 class="alert alert-success" style="text-align: center; margin-bottom: 0px;">New Student Admission form - <b>PG Students</b></h5>
		</div>
		<div class="panel-body">
			<div class="panel panel-default">
				<div class="panel-heading"  style="background-color: #f39c12">
					1.PERSONAL INFORMATION
				</div>
				<div class="panel-body">
					<div class="container-fluid">
						<div class="panel panel-danger ">
							<div class="panel-heading" style="background-color: #8DF;">
								1.1. Basic User Information
							</div>
							<!-- ,'action'=>'/user1/create' -->
							<div class="panel-body">
								<?php $form = ActiveForm::begin(['id'=>'modelUser','enableAjaxValidation'=>true,'validationUrl'=>'/admit-student/validate-user','options'=>[]]); ?>
								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-default panel-sm">
											<div class="panel-heading" style="background-color: #8DF;">
												1.1.1. Full Name
											</div>
											<div class="panel-body">
												<div class="row">
													<div class="col-md-3">
														<?= $form->field($modelUser, 'first_name')->textInput()->label('First Name') ?>
													</div>
													<div class="col-md-3">
														<?= $form->field($modelUser, 'middle_name')->textInput()->label('Middle Name') ?>
													</div>
												
													<div class="col-md-3">
														<?= $form->field($modelUser, 'last_name')->textInput()->label('Last Name') ?>
													</div>
													<div class="col-md-3">
														<?= $form->field($modelUser, 'gender')->radioList([
							                                'Female'=>'FEMALE',
							                                'Male'=>'MALE',
							                            ],['required'=>true,'value'=>'Female'])->label('Gender') ?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="panel panel-default panel-sm">
											<div class="panel-heading" style="background-color: #8DF;">
												1.1.2. Date of Birth
											</div>
											<div class="panel-body">
												<div class="row">
													<div class="col-md-6">
														<?= $form->field($modelUser, 'dob')->widget(DatePicker::className(),[
									                        'pluginOptions'=>[
									                            'autoClose'=>false,
									                            'format'=>'yyyy-m-dd',
									                            ],
									                        ])->label('Date Of Birth (G.C)')?>
													</div>
													<div class="col-md-6">
														<?= $form->field($modelUser, 'dob_ET')->widget(DatePicker::className(),[
									                        'pluginOptions'=>[
									                            'autoClose'=>true,
									                            'format'=>'yyyy-m-dd',
									                            ],
									                        ])->label('Date of Birth (E.C)')?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="panel panel-default">
											<div class="panel-heading" style="background-color: #8DF;">
												1.1.3. User Type and Nationality, Email
											</div>
											<div class="panel-body">
												<div class="row">
													<div class="col-md-3">
														<?= $form->field($modelUser, 'user_type')->dropdownList([
								                                'MILITARY'=>'  Military  ',
								                                'CIVIL'=>'  Civil  ',
								                                'FOREGN'=>'   Foreign  ',
								                                'FP'=>'   Federal Police  ',
								                                'ANOTHER'=>'  Another  ',
							                            ],['prompt'=>'Select user type'])->label('User Type')  ?>
													</div>
													<div class="col-md-2">
														<?= $form->field($modelUser, 'nationality')->dropdownList(
															ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
															['prompt'=>'Select Nationality']
														)->label('Nationality')?>
													</div>
													<div class="col-md-2">
														<?= $form->field($modelUser, 'phone_home')->textInput(['required'=>true])->label('Phone-Home')?>
													</div>

													<div class="col-md-2">
														<?= $form->field($modelUser, 'phone_mobile')->textInput(['required'=>true])->label('Phone-Mobile')?>
													</div>
													<div class="col-md-3">
														<?= $form->field($modelUser, 'email')->textInput()->label('Email')?>

														<?php echo Html::activeHiddenInput($modelUser, "place_of_birth_address_relId",['class'=>'place_of_birth_address_relId']);?>
														<?php echo Html::activeHiddenInput($modelUser, "place_of_residence_address_relId",['class'=>'place_of_residence_address_relId']);?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php ActiveForm::end(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading"  style="background-color: #f39c12">
					2. EDUCATIONAL BACKGROUND
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-danger ">
								<div class="panel-heading" style="background-color: #8DF;">
								2.1. Secondary Schools attended
							    </div>
								<div class="panel-body" id="body11">
									<!-- 'action'=>'/secondary-school-attended/create', -->
									<div class="row" id="secondary-school-attended">
										<?php $form = ActiveForm::begin(['id'=>'modelSSA','enableAjaxValidation'=>true,'validationUrl'=>'/pg-secondary-schools-attended/validate','options'=>[]]);?>
										<?php DynamicFormWidget::begin([
									                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
									                'widgetBody' => '.container-ssass', // required: css class selector
									                'widgetItem' => '.ssas', // required: css class
									                'limit' => 10, // the maximum times, an element can be cloned (default 999)
									                'min' => 1, // 0 or 1 (default 1)
									                'insertButton' => '.add-ssas', // css class
									                'deleteButton' => '.remove-ssas', // css class
									                'model' => $modelSSA[0],
									                'formId' => 'modelSSA',
									                'formFields' => [
									                    'ssa_school_name',
									                    'ssa_town',
									                    'ssa_year',
									                    'ssa_gradeLevels',
									                ],
									            ]); ?>

									    <div class="container-ssass"><!-- widgetContainer -->
										    <?php foreach ($modelSSA as $i => $modelSSA1): ?>
										    <div class="ssas panel panel-default"><!-- widgetBody -->
										        <div class="panel-heading">
											        <h3 class="panel-title pull-left">Secondary School Attended - (High School)</h3>
											        <div class="pull-right">
											        <button type="button" title="Add more" class="add-ssas btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
											        <button type="button" title="Remove" class="remove-ssas btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
							                        </div>
							                        <div class="clearfix"></div>
							                    </div>
							                    <div class="panel-body">
							                    	<div class="row">
								                        <?php
								                            // necessary for update action.
								                            if (! $modelSSA1->isNewRecord) {
								                                echo Html::activeHiddenInput($modelSSA1, "[{$i}]id");
								                            }

									                            echo Html::activeHiddenInput($modelSSA[0], "[{$i}]pssa_pei_relId",['class'=>'hidden_input_1']);
									                        ?>
															<div class="col-md-3">
																<?= $form->field($modelSSA1, "[{$i}]pgssa_name_of_school")->textInput(['maxlength' => true])->label('Name of High School') ?>
															</div>

															<div class="col-md-3">
																<?= $form->field($modelSSA1, "[{$i}]pgssa_location")->textInput(['maxlength' => true,['placeholder'=>'e.g. Wolkite']])->label('Location') ?>
															</div>
															<div class="col-md-2">
																<?= $form->field($modelSSA1, "[{$i}]pgssa_years_from")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2011']])->label('From -Year (E.C)') ?>
															</div>
															<div class="col-md-2">
																<?= $form->field($modelSSA1, "[{$i}]pgssa_years_to")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2011']])->label('To - Year (E.C)') ?>
															</div>
								                        </div><!-- .row -->
								                    </div>
											    </div>
											    <?php endforeach; ?>
											</div>
											<?php DynamicFormWidget::end(); ?>
									    <?php ActiveForm::end(); ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-danger">
								<div class="panel-heading" style="background-color: #8DF;">
									2.2. Post Secondary Education - COLLEGES
									<span class="bg-info" style="font-style: italic;">
										Write your overall educational background in form below
									</span>
								</div>
								<!-- 'action'=>'/post-secondary-education/create', -->
								<div class="panel-body">
									<?php $form = ActiveForm::begin(['id'=>'modelPSE','enableAjaxValidation'=>true,'validationUrl'=>'/pg-post-secondary-school/validate','options'=>[]]); ?>
									<div class="row" id="post-secondary-education">
										<div class="panel-body">
								            <?php DynamicFormWidget::begin([
								                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
								                'widgetBody' => '.container-pses', // required: css class selector
								                'widgetItem' => '.pse', // required: css class
								                'limit' => 10, // the maximum times, an element can be cloned (default 999)
								                'min' => 1, // 0 or 1 (default 1)
								                'insertButton' => '.add-pse', // css class
								                'deleteButton' => '.remove-pse', // css class
								                'model' => $modelPSE[0],
								                'formId' => 'modelPSE',
								                'formFields' => [
								                    'pgpss_institute_name',
								                    'pgpss_location',
								                    'pgpss_deploma_awarded',
								                    'pgpss_date_of_award',
								                ],
								            ]); ?>

									        <div class="container-pses"><!-- widgetContainer -->
									            <?php foreach ($modelPSE as $i => $modelPSE1): ?>
									                <div class="pse panel panel-default"><!-- widgetBody -->
									                    <div class="panel-heading">
									                        <h3 class="panel-title pull-left">Post Secondary School - COLLEGE</h3>
									                        <div class="pull-right">
									                            <button type="button" title="Add more" class="add-pse btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
									                            <button type="button" title="Remove" class="remove-pse btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
									                        </div>
									                        <div class="clearfix"></div>
									                    </div>
									                    <div class="panel-body">
									                    	<div class="row">
										                        <?php
										                            // necessary for update action.
										                            if (! $modelPSE1->isNewRecord) {
										                                echo Html::activeHiddenInput($modelPSE1, "[{$i}]id");
										                            }
										                            echo Html::activeHiddenInput($modelPSE[0], "[{$i}]ppss_pei_relId",['class'=>'hidden_input_1']);
										                        ?>
										                        <div class="col-md-3">
																	<?= $form->field($modelPSE1, "[{$i}]pgpss_institute_name")->textInput()->label('Institute Name') ?>
																</div>
																<div class="col-md-3">
																	<?= $form->field($modelPSE1, "[{$i}]pgpss_location")->textInput()->label('Location') ?>
																</div>
																<div class="col-md-3">
																	<?= $form->field($modelPSE1, "[{$i}]pgpss_deploma_awarded")->textInput()->label('Deploma Awarded') ?>
																</div>	
																<div class="col-md-3">
																	<?= $form->field($modelPSE1, "[{$i}]pgpss_date_of_award")->widget(DatePicker::className(),[
												                        'pluginOptions'=>[
												                            'autoClose'=>false,
												                            'format'=>'yyyy-m-dd',
												                            ],
												                        ])->label('Date of award') ?>
																</div>
									                        </div><!-- .row -->
									                    </div>
									                </div>
									            <?php endforeach; ?>
									        </div>
									        <?php DynamicFormWidget::end(); ?>
									    </div>
									</div>
									<?php ActiveForm::end(); ?>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-danger">
								<div class="panel-heading" style="background-color: #8DF;">
									2.3. Education Information - Write Your Educational Information here
								</div>
								<!-- 'action'=>'/education-information/create', -->
								<div class="panel-body">
									<?php $form = ActiveForm::begin(['id'=>'modelEI','validationUrl'=>'/pg-education-informationt/validate-pg','options'=>[]]); ?>
										<div class="row">
											<div class="col-md-4">
												<?= $form->field($modelEI, 'pgei_UG_university_name')->textInput() ?>
											</div>
											<div class="col-md-2">
												<?= $form->field($modelEI, 'pgei_CGPA')->textInput() ?>
											</div>
											<div class="col-md-6">
												<?= $form->field($modelEI, 'pgei_field_of_study')->textInput() ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<?= $form->field($modelEI, 'pgei_major')->textInput() ?>
											</div>
											<div class="col-md-6">
												<?= $form->field($modelEI, 'pgei_minor')->textInput() ?>
											</div>
										</div>

										<div class="row">	
											<div class="col-md-6">
												<?= $form->field($modelEI, 'pgei_work_experience')->textarea(['rows' => 8,'placeholder'=>'State the kind of work you have been engaged in.']) ?>
											</div>
											<div class="col-md-6">
												<?= $form->field($modelEI, 'pgei_reserches')->textarea(['rows' => 8,'placeholder'=>'Give a particular of any relevant experience in research or industry. Give references to any published work you have done. Enclose if available..']) ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<?= $form->field($modelEI, 'pgei_awards')->textarea(['rows' => 4,'placeholder'=>'State the type of awards you have in recognition to your merit.']) ?>
											</div>
										</div>
									<?php ActiveForm::end(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading"  style="background-color: #f39c12">
					3. Emergency Contact Information<br>
					<span class="bg-info" style="font-style: italic; font-size: 9pt;">Give name and address of of person's preferably undergraduate instructors, employers and pofessional associations to whom references can be made about your ablity. Specify your associaition with each referee.</span>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-danger">
								<div class="panel-heading" style="background-color: #8Df;">
									3.1. Emergency Contact Basic Inforamtion
								</div>
								<!-- ,'action'=>'/emergency-contact/create' -->
								<div class="panel-body">
									<?php $form = ActiveForm::begin(['id'=>'modelEC','enableAjaxValidation'=>true,'validationUrl'=>'/emergency-contact/validate','options'=>[]]); ?>
									<div class="row">
										<div class="col-md-2">
											<?php echo Html::activeHiddenInput($emergencyContact, "ec_address_relId",['class'=>'hidden_input_2']);?>
											<?= $form->field($emergencyContact, 'ec_first_name')->textInput()->label('First Name') ?>
										</div>
									
										<div class="col-md-2">
											<?= $form->field($emergencyContact, 'ec_last_name')->textInput()->label('Last Name') ?>
										</div>
									
										<div class="col-md-2">
											<?= $form->field($emergencyContact, 'ec_relationShip')->textInput()->label('Relation ship') ?>
										</div>
									
										<div class="col-md-2">						    

								    		<?= $form->field($emergencyContact, 'ec_phone_home')->textInput()->label('Phone - Home') ?>

										</div>
									
										<div class="col-md-2">
											<?= $form->field($emergencyContact, 'ec_phone_mobile')->textInput()->label('Phone - Mobile') ?>
										</div>
									
										<div class="col-md-2">
											<?= $form->field($emergencyContact, 'ec_email')->textInput()->label('Email') ?>
										</div>
									</div>
									<?php ActiveForm::end(); ?>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="panel panel-danger">
								<div class="panel-heading" style="background-color: #8Df;">
									3.2. Emergency Contact Address Info
								</div>
								<!-- ,'action'=>'/address-info/create' -->
								<div class="panel-body">
									<?php $form = ActiveForm::begin(['id'=>'modelAddress3','enableAjaxValidation'=>true,'validationUrl'=>'/address-info/validate','options'=>[]]); ?>

									<div class="row">
										<div class="col-md-2">
											<?= $form->field($modelAddress, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
												[
													'prompt'=>'Select Arrdess - Country'
												])->label('Country') ?>
										</div>
										<div class="col-md-2">
											<?= $form->field($modelAddress, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),
												[
													'prompt'=>'Select Arrdess - Region'
												])->label('Region') ?>
										</div>
										<div class="col-md-2">
											<?= $form->field($modelAddress, 'address_zone')->textInput()->label('Zone') ?>
										</div>
										<div class="col-md-2">
										 	<?= $form->field($modelAddress, 'address_woreda')->textInput()->label('Woreda') ?>
										</div>
										<div class="col-md-2">
											<?= $form->field($modelAddress, 'address_town')->textInput()->label('Town') ?>
										</div>
										<div class="col-md-2">
											<?= $form->field($modelAddress, 'address_kebele')->textInput()->label('Kebele') ?>
										</div>
										<div class="col-md-2">
											<?= $form->field($modelAddress, 'address_hourse_no')->textInput()->label('House Number') ?>
										</div>
									</div>
								    <?php ActiveForm::end(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading"  style="background-color: #f39c12">
					4. CLASSFICATION OF ADMISSION
				</div>
				<!-- 'action'=>'/student1/create', -->
				<div class="panel-body">
					<?php $form = ActiveForm::begin(['id'=>'modelStudent','enableAjaxValidation'=>true,'validationUrl'=>'/admit-student/validate-student','options'=>[]]); ?>
					<div class="container-fluid">
						<div class="panel panel-danger">
							<div class="panel-heading" style="background-color: #8Df;">
								4.1. Apply for admission and Program:
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3">
										<?php  echo Html::activeHiddenInput($modelStudent, "std_user_relId",['class'=>'std_user_relId']);
										?>
										<?php  echo Html::activeHiddenInput($modelStudent, "std_emergency_contact_relId",['class'=>'std_emergency_contact_relId']);
										?>
										<?php  echo Html::activeHiddenInput($modelStudent, "st_educ_info_relId",['class'=>'st_educ_info_relId']);
										?>
										<?= $form->field($modelStudent, 'std_admission_relId')->dropdownList(
					                          ArrayHelper::map(\backend\models\Admission::find()->where(['admission_name'=>'Regular'])->all(),'id','admission_name'),
					                          [
					                          'readOnly'=>true,
					                      ])->label('Admission - Modality of The Study') ?>

									</div>
									<div class="col-md-3">
										<?= $form->field($modelStudent, 'std_prog_relId')->dropdownList(
					                          ArrayHelper::map(\backend\models\Program::find()->where(['program_name'=>'Masters'])->all(),'id','program_name'),
					                          [
					                          'readOnly'=>true,
					                      ])->label('Program - Applied for') ?>
									</div>

									<div class="col-md-3">
										<?= $form->field($modelStudent, 'std_dept_relId')->dropdownList(
					                          ArrayHelper::map(\backend\models\Department::find()->where(['<>','dept_name','Basic and Applied Department'])->all(),'id','dept_name'),
					                          [
					                          'prompt'=>'Select Department',
					                          'required'=>true,
					                          'onchange'=>'
					                          		$.post("/department/speclists",
					                          			{
					                          				deptId:$(this).val(),
					                          				admId:$("#student1-std_admission_relid").val(),
					                          				progId:$("#student1-std_prog_relid").val()

					                          			}).done(function(data){

					                          				$("#student1-std_spec_relid").html(data);
					                          	});
					                          ',
					                      ])->label('Department') ?>
									</div>

									<div class="col-md-3">
										<?= $form->field($modelStudent, 'std_spec_relId')->dropdownList(
					                          [],
					                          [
					                          'required'=>true,
					                          'prompt'=>'Select Specialization',
					                      ])->label('Specialization') ?>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-danger">
							<div class="panel-heading" style="background-color: #8Df;">
								4.2. FINANCIAL SUPPORT
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3">
		                                <?= $form->field($modelStudent, 'std_type')->dropDownList(
		                                    [
		                                        'Military'=>'Military',
		                                        'Civil'=>'Civil',
		                                        'Federal Police'=>'Federal Police',
		                                        'Foreign'=>'Foreign'
		                                    ], 
		                                    ['prompt' => 'Select Student type', ]
		                            )?>
		                            </div>
									<div class="col-md-6">
										<?= $form->field($modelStudent, 'std_schlrshpType_relId')->dropDownList(
				                                ArrayHelper::map(\backend\models\ScholarshipType::find()->all(), 'id', 'schType_name'
				                                ),
				                                ['prompt' => 'Select Sponsorship Type', ]
				                        )?>
									</div>
									<div class="col-md-6">
					                    <?php  echo Html::activeHiddenInput($modelStudent, "std_admissionEntries",['class'=>'std_admissionEntries']);
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php ActiveForm::end(); ?>
				</div>
			</div>

			<div class="panel panel-danger">
				<div class="panel-heading">
					5.Confirm and Save
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-10">
							<span style="font-style: italic; font-weight: bold;">I hereby certify that all information given in this document is complete and accurate.</span>
							<input type="checkBox" id="ghjgfsjdfvdfksdgfgdshf" name="" class="pull-right">
						</div>
						<div class="col-md-2"  style="display: none;" id="dhgksdfkgjhdfgshgfid">
							<button class="btn btn-success btn-md" id="gdhgfsgdfghsdgh" onclick="return false;" title="Save form">Save</button>
						</div>
						<div class="col-md-12">
							<div class="container-fluid" id="status_message">
								
							</div>
							<div class="container-fluid" id="status_status">
								
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>

<?php

 Modal::begin([
    'header'=>'<h4 class="bg-success">Student Admission Completed!!</h4>',
    'id'=>'modal_success',
    'size'=>'modal-sm',
    'options'=>[
        'style'=>[
            'text-align'=>'left',
        ],
    ],
 ]);
 echo "<div id='modalContent' class='jumbotron'>Loading user account form...</div>";
 echo "<div id='modalFooter' class='alert alert-success'></div>";
 Modal::end();
 ?>

<?php
	$this->registerJs(
	"

	eiId = null;
	ssas = null;
	pse = null;
	eem = null;
	address1 = null;//Place of birth
	ecId = null;//emergency contact id.
	uid = null;//User ID
	OTP = null;
	step = 0;

	window.submitStudentInfo = function(){
		// e.preventDefault();
		if(step == 6 && uid != null && OTP != null){

			// console.log('INNNN');

			form = $('#modelStudent');
			admEntry = JSON.stringify([eiId,address1,ecId,uid]);
			arrayChoice = 'Not Needed for this Program';
			$('.std_deptChoice').val(arrayChoice);
			$('.std_admissionEntries').val(admEntry);
			$.ajax({
				type: 'POST',
                url: '/student1/create',
                data: form.serialize(),
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){
                			$('#status_message').html('Student:  :: '+data).focus();  
                			// ecId = d[1];  
                			// OTP = d[2];          			
                			step = 0;

                			$('#modal_success').modal('show');
                			$('#modalContent').html('<p>Admission Successfully Completed.</p><p>Please Review Your account activation status via the code provided below. Please Keep it secreet as much as possible.</p><pre><u>OTP</u></pre> <span style=\"background-color: #333; color:white;\">'+OTP+'<span>');

                			$('#modal_success').on('hide.bs.modal',function(){
					           window.location.href = window.location;
					        });

                		}else{
                			$('#status_message').html('Error: '+data).focus();
                			$('#status_status').html('Error: '+data).focus();

                			// for now...

       						rollBack('modelUser',uid);
							rollBack('modelEC',ecId);
				   			rollBack('modelAddress',address1);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
                		rollBack('modelUser',uid);
						rollBack('modelEC',ecId);
			    		rollBack('modelAddress',address1);
						rollBack('modelEI',eiId);
                	}
                }
			});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}

	// 
	window.submitUserInfo = function(){
		// e.preventDefault();
		if(step == 5 && ecId != null){
			form = $('#modelUser');
			$.ajax({
				type: 'POST',
                url: '/user1/create',
                data: form.serialize(),
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){
                			$('#status_message').html('User  :: '+data).focus();  
                			uid = d[1];
                			OTP = d[2];
                			$('.std_user_relId').val(uid);  			
                			step++;

                			console.log(step,uid , OTP);
                			submitStudentInfo();

                		}else{
                			$('#status_message').html('Error: '+data).focus();
                			rollBack('modelEC',ecId);
	                		rollBack('modelAddress',address1);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_message').html('ERROR: '+data).focus();
                		rollBack('modelEC',ecId);
                		rollBack('modelAddress',address1);
						rollBack('modelEI',eiId);
			    	}
                }
			});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}

	window.submitEmergencyContact = function(){
		// e.preventDefault();
		if(step == 4 && address1 != null){
			form = $('#modelEC');
			$.ajax({
				type: 'POST',
                url: '/emergency-contact/create',
                data: form.serialize(),
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){
                			$('#status_message').html('Success-EC: '+data).focus();  
                			ecId = d[1];
                			$('.std_emergency_contact_relId').val(ecId);
                			$('.st_educ_info_relId').val(eiId);
                			step++;
                			// submitUserInfo();

                			submitUserInfo();

                		}else{
                			$('#status_status').html('Error: '+data).focus();
                			rollBack('modelAddress',address1);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_message').html('ERROR: '+data).focus();
                		rollBack('modelAddress',address1);
						rollBack('modelEI',eiId);
                	}
                }
			});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}
	window.submitAddress1 = function(){
		// e.preventDefault();
		if(step == 3 && pse != null){
			form = $('#modelAddress3');
			$.ajax({
				type: 'POST',
                url: '/address-info/create',
                data: form.serialize(),
                async: false,
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){
                			$('#status_message').html('Success: '+data).focus();  
                			address1 = d[1];   
                			// $('.place_of_birth_address_relId').val(d[1]);    
                			$('.hidden_input_2').val(address1);     			
                			step++;
                			submitEmergencyContact();
                		}else{
                			$('#status_status').html('Error: '+data).focus();

                			rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
                		rollBack('modelEI',eiId);
                	}
                }
			});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}

	window.submitPSE = function(){
		// e.preventDefault();
		if(step == 2 && ssas != null){
			form = $('#modelPSE');
			$.ajax({
    			type: 'POST',
                url: '/pg-post-secondary-school/create',
                data: form.serialize(),
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){
                			
                			pse = d[1];
                			step++;
                			submitAddress1();
                		}else{
                			$('#status_status').html(data).focus();

                			rollBack('modelSSA',ssas);
                			rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
                		rollBack('modelSSA',ssas);
                		rollBack('modelEI',eiId);
                	}
                },
                error:function(data){
                	rollBack('modelSSA',ssas);
                	rollBack('modelEI',eiId);
                }
    		});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}

	window.submitSSA = function(){
    	// e.preventDefault();
    	if(step == 1 && eiId != null){
    		form = $('#modelSSA');
    		formID = form.id;
    		action = form.action;

    		$.ajax({
    			type: 'POST',
                url: '/pg-secondary-schools-attended/create',
                data: form.serialize(),
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){

                			ssas = d[1];
                			step++;

                			submitPSE();


             	//			if($('#attendedPSE_id :checked').val() == 1){
	            // 				submitPSE();
	        				// }else{
	        				// 	pse = 0;
	            //     			step++;
	            //     			submitEEM();
	        				// }
                			
                		}else{
                			$('#status_status').html(data).focus();
                			rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
                		rollBack('modelEI',eiId)
                	}
                }
    		});
    	}else{
    		console.log('Expected 9 : GOT '+step);
    	}
    }

	window.submitEI = function(){
    	// e.preventDefault();
    	if(step == 0){
    		form = $('#modelEI');
	    	// console.log(form);
	    	$.ajax({
	            type: 'POST',
	            url: '/pg-education-info/create',
	            data: form.serialize(),
	            success: function (data) {  
	                try{
	            		d = JSON.parse(data);
	            		if(d[0] == 1){
	            			$('#status_message').html('<br>Education Inforamtion ID => '+d[1]);
	            			
	            			$(' #postsecondaryeducation-ppse_ei_relid, #secondaryschoolattended-ssa_ei_relid, #enteranceexammark-eem_ei_relid' ).val(d[1]);
	            			
	            			$('.hidden_input_1').val(d[1]);

	            			eiId = d[1];
	            			console.log(eiId);
	            			step++;
	            			submitSSA();	
	            		}
	        		}catch(e){
	        			$('#status_status').html(data).focus();
	        			// rollBack()
	        		}
	            },
	            error: function(jqXHR, text, error){
	            	$('#status_status').html(text).focus();
	            	// rollBack()
	            }
	        });
    	}else{
    		console.log('Expected 0 : GOT '+step);
    	}
    }


	$('#gdhgfsgdfghsdgh').on('click',function(e){
    	e.preventDefault();
    	if(submit_form_validate() == true){
    		return submitEI();
    	}else{
    		console.log('FALSE');
    		// return undefined;
    	}    	
    });

     window.rollBack = function(actionId, IDS){
    	if(actionId != undefined && IDS != undefined){
    		$.ajax({
            	type: 'POST',
            	url: '/admit-student/roll-back-pg',
            	data: {actionId:actionId,ids:IDS},
            	success: function (data) {  
                	$('#status_message').html('Rolling back....  '+data).focus();
            	},
            	error: function(jqXHR, text, error){
            		$('#status_message').html(text).focus();
            	}
        	});
    	}else{
    		console.log('Already completed...');
    	}
    }

    function submit_form_validate(){
    	valid = false;
    	
    	$('#modelUser').data('yiiActiveForm').submitting = true;
    	valid = $('#modelUser').yiiActiveForm('validate');

    	console.log('MODEL_USER => '+valid);

    	$('#modelStudent').data('yiiActiveForm').submitting = true;
    	valid = $('#modelStudent').yiiActiveForm('validate');
    	console.log('MODEL_STUDENT => '+valid);

    	// $('#modelAddress1').data('yiiActiveForm').submitting = true;
    	// valid = $('#modelAddress1').yiiActiveForm('validate');
    	// console.log('MODEL_ADDRESS_1 => '+valid);

    	// $('#modelAddress2').data('yiiActiveForm').submitting = true;
    	// valid = $('#modelAddress2').yiiActiveForm('validate');
    	// console.log('MODEL_ADDRESS_2 => '+valid);

    	$('#modelAddress3').data('yiiActiveForm').submitting = true;
    	valid = $('#modelAddress3').yiiActiveForm('validate');
    	console.log('MODEL_ADDRESS_3 => '+valid);

    	$('#modelSSA').data('yiiActiveForm').submitting = true;
    	valid = $('#modelSSA').yiiActiveForm('validate');
    	console.log('MODEL_SSA => '+valid);

    	$('#modelPSE').data('yiiActiveForm').submitting = true;
		valid = $('#modelPSE').yiiActiveForm('validate');
		console.log('MODEL_PSE => '+valid);


    	$('#modelEI').data('yiiActiveForm').submitting = true;
    	valid = $('#modelEI').yiiActiveForm('validate');
    	console.log('MODEL_EI => '+valid);

    	$('#modelEC').data('yiiActiveForm').submitting = true;
    	valid = $('#modelEC').yiiActiveForm('validate');
    	console.log('MODEL_EC => '+valid);
    	
    	return (typeof valid != 'undefined');
    }

	shown = true;
	$('#ghjgfsjdfvdfksdgfgdshf').on('change',function(event){

		if(shown){
			$('#dhgksdfkgjhdfgshgfid').show();
			shown = false;
		}else{
			$('#dhgksdfkgjhdfgshgfid').hide();
			shown = true;
		}
	});
	
	window.gettt = function(){
    	if($('#attendedPSE_id :checked').val() == 1){
    		$('#post-secondary-education').show();
    	}else{
    		$('#post-secondary-education').hide();
    	}
    }

	$('.form-control, .form-group').css('font-size','inherit');
	",
	$this::POS_READY);
?>