<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\Modal;

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

<h5 class="alert alert-success" style="text-align: center; margin-bottom: 0px;">New Student Admission form - <b>PG Students</b></h5>
<div class="container-fluid" style="justify-content: center; border: 1px dashed gold; background-color: #4F9; margin-top: 0px;">
	<div class="container-fluid" style="font-size: 9pt; font-weight: bold;">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						1.PERSONAL INFORMATION
					</div>
					<div class="panel-body">
						<div class="container-fluid">
							<div class="panel panel-warning">
								<div class="panel-heading" style="background-color: #8DF;">
									1.1. Basic User Information
								</div>
								<!-- ,'action'=>'/user1/create' -->
								<div class="panel-body">
									<?php $form = ActiveForm::begin(['id'=>'modelUser','enableAjaxValidation'=>true,'validationUrl'=>'/admit-student/validate-user','options'=>['onsubmit'=>'submitUserInfo();return false;']]); ?>
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
													1.1.3.Contact Information - Phone, Email, Nationality
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
								                            ])->label('User Type')  ?>
														</div>
														<div class="col-md-3">
															<?= $form->field($modelUser, 'nationality')->dropdownList(
																ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
																['prompt'=>'Select Nationality']
															)->label('Nationality')?>
														</div>
														<div class="col-md-3">
															<?= $form->field($modelUser, 'phone_mobile')->textInput()->label('Phone - Mobile')?>
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
									<div class="row">
										<div class="col-md-12">
											<div class="panel panel-default">
												<div class="panel-heading" style="background-color: #8DF;">
													1.1.4. Address Information - Place Of Birth
												</div>
												<!-- 'action'=>'/address-info/create', -->
												<div class="panel-body">
													<?php $form = ActiveForm::begin(['id'=>'modelAddress1','enableAjaxValidation'=>true,'validationUrl'=>'/address-info/validate','options'=>['onsubmit'=>'submitAddress1();return false;']]); ?>
													<div class="row">
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
																[
																	'prompt'=>'Select Address - Country'
																])
														->label('Country') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),
																[
																	'prompt'=>'Select Address - Region'
																])
														->label('Region') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_zone')->textInput()->label('Zone') ?>
														</div>
														<div class="col-md-4">
														 	<?= $form->field($modelAddress, 'address_woreda')->textInput()->label('Woreda') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_town')->textInput()->label('Town') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_kebele')->textInput()->label('Kebele') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_hourse_no')->textInput()->label('House Number') ?>
														</div>
													</div>
													<?php ActiveForm::end(); ?>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="panel panel-default">
												<div class="panel-heading" style="background-color: #8DF;">
													1.1.5. Address Information - Current Residential Address
												</div>
												<!-- ,'action'=>'/address-info/create' -->
												<div class="panel-body">
													<?php $form = ActiveForm::begin(['id'=>'modelAddress2','enableAjaxValidation'=>true,'validationUrl'=>'/address-info/validate','options'=>['onsubmit'=>'submitAddress2();return false;']]); ?>
													<div class="row">
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
																[
																	'prompt'=>'Select Address - Country'
																])->label('Country') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),
																[
																	'prompt'=>'Select Address - Region'
																])->label('Region') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_zone')->textInput()->label('Zone') ?>
														</div>
														<div class="col-md-4">
														 	<?= $form->field($modelAddress, 'address_woreda')->textInput()->label('Woreda') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_town')->textInput()->label('Town') ?>
														</div>
														<div class="col-md-4">
															<?= $form->field($modelAddress, 'address_kebele')->textInput()->label('Kebele') ?>
														</div>
														<div class="col-md-4">
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
							<div class="panel panel-warning">
								<div class="panel-heading" style="background-color: #8D0;">
									1.2. Emergency Contact Information
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-12">
											<div class="panel panel-danger">
												<div class="panel-heading" style="background-color: #8D0;">
													1.2.1. Emergency Contact Basic Inforamtion
												</div>
												<!-- ,'action'=>'/emergency-contact/create' -->
												<div class="panel-body">
													<?php $form = ActiveForm::begin(['id'=>'modelEC','enableAjaxValidation'=>true,'validationUrl'=>'/emergency-contact/validate','options'=>['onsubmit'=>'submitEmergencyContact();return false;']]); ?>
													<div class="row">
														<div class="col-md-3">
															<?php echo Html::activeHiddenInput($emergencyContact, "ec_address_relId",['class'=>'hidden_input_2']);?>
															<?= $form->field($emergencyContact, 'ec_first_name')->textInput()->label('First Name') ?>
														</div>
													
														<div class="col-md-3">
															<?= $form->field($emergencyContact, 'ec_last_name')->textInput()->label('Last Name') ?>
														</div>
													
														<div class="col-md-3">
															<?= $form->field($emergencyContact, 'ec_relationShip')->textInput()->label('Relation ship') ?>
														</div>
													
														<div class="col-md-3">						    

												    		<?= $form->field($emergencyContact, 'ec_phone_home')->textInput()->label('Phone - Home') ?>

														</div>
													
														<div class="col-md-3">
															<?= $form->field($emergencyContact, 'ec_phone_mobile')->textInput()->label('Phone - Mobile') ?>
														</div>
													
														<div class="col-md-3">
															<?= $form->field($emergencyContact, 'ec_email')->textInput()->label('Email') ?>
														</div>
													</div>
													<?php ActiveForm::end(); ?>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="panel panel-info">
												<div class="panel-heading" style="background-color: #8D0;">
													1.2.2. Emergency Contact Address Info
												</div>
												<!-- ,'action'=>'/address-info/create' -->
												<div class="panel-body">
													<?php $form = ActiveForm::begin(['id'=>'modelAddress3','enableAjaxValidation'=>true,'validationUrl'=>'/address-info/validate','options'=>['onsubmit'=>'submitAddress3();return false;']]); ?>

													<div class="row">
														<div class="col-md-2">
															<?= $form->field($modelAddress, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
																[
																	'prompt'=>'Select Address - Country'
																])->label('Country') ?>
														</div>
														<div class="col-md-2">
															<?= $form->field($modelAddress, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),
																[
																	'prompt'=>'Select Address - Region'
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
						</div>
					</div>
				</div>

				<div class="panel panel-warning">
					<div class="panel-heading">
						3. EDUCATIONAL BACKGROUND
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading" style="background-color: #8D0;">
										3.1. Secondary Schools Attended
									</div>
									<div class="panel-body" id="body11">
										<!-- 'action'=>'/secondary-school-attended/create', -->
										<div class="row" id="secondary-school-attended">
											<?php $form = ActiveForm::begin(['id'=>'modelSSA','enableAjaxValidation'=>true,'validationUrl'=>'/secondary-school-attended/validate','options'=>['onsubmit'=>'submitSSA();return false;']]);?>
											<div class="panel panel-default">
										        <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i> Secondary Schools attended</h4></div>
										        <div class="panel-body">
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
											                        <h3 class="panel-title pull-left">Secondary SchoolA Attended</h3>
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

												                            echo Html::activeHiddenInput($modelSSA1, "[{$i}]ssa_ei_relId",['class'=>'hidden_input_1']);
												                        ?>
																		<div class="col-md-3">
																			<?= $form->field($modelSSA1, "[{$i}]ssa_school_name")->textInput(['maxlength' => true])->label('School Name') ?>
																		</div>
																		<div class="col-md-3">
																			<?= $form->field($modelSSA1, "[{$i}]ssa_town")->textInput(['maxlength' => true,['placeholder'=>'e.g. Wolkite']])->label('Town') ?>
																		</div>
																		<div class="col-md-2">
																			<?= $form->field($modelSSA1, "[{$i}]ssa_year")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2011']])->label('Year (E.C)') ?>
																		</div>
																		<div class="col-md-4 pull-right" title='List your secodary schools Attended.' style="float: right; text-align: right;">
																			<?php $values = [
																					'9'=>'Grade 9',
													                                '10'=>'Grade 10',
													                                '11'=>'Grade 11',
													                                '12'=>'Grade 12',
													                                '10+1'=>'10+1',
													                                '10+2'=>'10+2',
													                            ]; ?>
																			<?= $form->field($modelSSA1, "[{$i}]ssa_gradeLevels")->dropdownList(
													                            $values,
													                            ['prompt'=>'Select Grade level attended.']
													                        )->label('Grade Level Attended')?>
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
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading" style="background-color: #8D0;">
										3.2. Ethiopian Higher Education Enterance Examination (EHEEE) Information
										<br>
										<span class="bd-info" style="font-size: 7pt; font-style: italic; font-weight: normal;">(Write five subjects with the best grades earned; <b>English</b>, <b>Maths</b>, and <b>Science(s)</b> must be included):
										</span>
									</div>
									<div class="panel-body">
										<!-- ,'action'=>'/enterance-exam-mark/create' -->
										<div class="row" id="enterance-exam-mark">
											<?php $form = ActiveForm::begin(['id'=>'modelEEM','enableAjaxValidation'=>true,'validationUrl'=>'/enterance-exam-mark/validate','options'=>['onsubmit'=>'submitEEM();return false;']]); ?>

											<div class="panel panel-default">
										        <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i> EHEEE Information</h4></div>
										        <div class="panel-body">
										             <?php DynamicFormWidget::begin([
										                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
										                'widgetBody' => '.container-eems', // required: css class selector
										                'widgetItem' => '.eem', // required: css class
										                'limit' => 10, // the maximum times, an element can be cloned (default 999)
										                'min' => 1, // 0 or 1 (default 1)
										                'insertButton' => '.add-eem', // css class
										                'deleteButton' => '.remove-eem', // css class
										                'model' => $modelEEM[0],
										                'formId' => 'modelEEM',
										                'formFields' => [
										                    'eem_subject',
										                    'eem_reference_number',
										                    'eem_year',
										                    'eem_markObtained',
										                ],
										            ]); ?>

										            <div class="container-eems"><!-- widgetContainer -->
										            <?php foreach ($modelEEM as $i => $modelEEM1): ?>
										                <div class="eem panel panel-default"><!-- widgetBody -->
										                    <div class="panel-heading">
										                        <h3 class="panel-title pull-left">EHEEE Subject Mark Information</h3>
										                        <div class="pull-right">
										                            <button type="button" class="add-eem btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
										                            <button type="button" class="remove-eem btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
										                        </div>
										                        <div class="clearfix"></div>
										                    </div>
										                    <div class="panel-body">
										                    	<div class="row">
											                        <?php
											                            // necessary for update action.
											                            if (! $modelEEM1->isNewRecord) {
											                                echo Html::activeHiddenInput($modelEEM1, "[{$i}]id");
											                            }
											                            echo Html::activeHiddenInput($modelEEM[0], "[{$i}]eem_ei_relId",['class'=>'hidden_input_1']);
											                        ?>
																	<div class="col-md-3">
																		<?= $form->field($modelEEM1, "[{$i}]eem_subject")->textInput(['maxlength' => true])->label('Subject') ?>
																	</div>
																	<div class="col-md-3">
																		<?= $form->field($modelEEM1, "[{$i}]eem_reference_number")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2004']])->label('Reference Number') ?>
																	</div>
																	<div class="col-md-2">
																		<?= $form->field($modelEEM1, "[{$i}]eem_year")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2011']])->label('Year (E.C)') ?>
																	</div>
																	<div class="col-md-2">
																		<?= $form->field($modelEEM1, "[{$i}]eem_markObtained")->textInput()->label('Mark Obtained') ?>
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
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading" style="background-color: #8D0;">
										3.3. Education Information - Documents and Enterance Mark
									</div>
									<!-- 'action'=>'/education-information/create', -->
									<div class="panel-body">
										<?php $form = ActiveForm::begin(['id'=>'modelEI','validationUrl'=>'/education-informationt/validate','options'=>['onsubmit'=>'submitEI();return false;']]); ?>
											<div class="row">
												<div class="col-md-3">
													<?= $form->field($modelEI, "ei_enteranceMarkPoint")->textInput(['maxlength' => true])->label('Enterance mark') ?>
												</div>
											</div>
										<?php ActiveForm::end(); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading" style="background-color: #8D0;">
										3.4. Post Secondary Education
									</div>
									<!-- 'action'=>'/post-secondary-education/create', -->
									<div class="panel-body">
										<?php $form = ActiveForm::begin(['id'=>'modelPSE','enableAjaxValidation'=>true,'validationUrl'=>'/post-secondary-education/validate','options'=>['onsubmit'=>'submitPSE();return false;']]); ?>
										<div class="row">
											<div class="col-md-9">
												Have you ever been enrolled in any post-secondary educational institution(s) (University or College level) in Ethiopia or abroad?
											</div>
											<div class="col-md-3">
												<?= $form->field($modelEEM[0], 'attendedPSE')->radioList(
						                            [
						                                1=>' YES ',
						                                0=>' NO ',
						                            ],
						                            [
						                            	'id'=>'attendedPSE_id',
						                            	'value'=>0,
						                                'title'=>'Attended Post Secondary Education',
						                                'onchange'=>'gettt();',
						                            ]
						                        )->label(false) ?>
											</div>
										</div>
										<div class="row" id="post-secondary-education"  style="display: none;">
											<div class="panel panel-default">
										        <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i> Post Secondary School</h4></div>
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
										                    'pse_institute_name',
										                    'pse_CGPA_obtained',
										                    'pse_from_year',
										                    'pse_to_year',
										                    'pse_credit_earned',
										                    'pse_certificate_earned',
										                    'pse_nationality_relId',
										                ],
										            ]); ?>

										            <div class="container-pses"><!-- widgetContainer -->
										            <?php foreach ($modelPSE as $i => $modelPSE1): ?>
										                <div class="pse panel panel-default"><!-- widgetBody -->
										                    <div class="panel-heading">
										                        <h3 class="panel-title pull-left">Post Secondary School</h3>
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
											                            echo Html::activeHiddenInput($modelPSE[0], "[{$i}]pse_ei_relId",['class'=>'hidden_input_1']);
											                        ?>
											                        <div class="col-md-3">
																		<?= $form->field($modelPSE1, "[{$i}]pse_institute_name")->textInput()->label('Institute Name') ?>
																	</div>
																	<div class="col-md-3">
																		<?= $form->field($modelPSE1, "[{$i}]pse_nationality_relId")->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
																			[
																				'prompt'=>'Seelct Country',
																			])->label('Country') ?>
																	</div>
																	<div class="col-md-2">
																		<?= $form->field($modelPSE1, "[{$i}]pse_from_year")->textInput(['maxlength' => true])->label('From Year') ?>
																	</div>
																	<div class="col-md-2">
																		<?= $form->field($modelPSE1, "[{$i}]pse_to_year")->textInput()->label('To Year') ?>
																	</div>
																	<div class="col-md-2">
																		<?= $form->field($modelPSE1, "[{$i}]pse_credit_earned")->textInput(['placeholder'=>'e.g. 85'])->label('Credit Earned') ?>
																	</div>
																	<div class="col-md-2">
																		<?= $form->field($modelPSE1, "[{$i}]pse_CGPA_obtained")->textInput(['placeholder'=>'e.g. 3.98'])->label('CGPA') ?>
																	</div>	
																	<div class="col-md-3">
																		<?= $form->field($modelPSE1, "[{$i}]pse_certificate_earned")->textInput()->label('Certificate Earned') ?>
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
						</div>
					</div>
				</div>

				<div class="panel panel-primary">
					<div class="panel-heading">
						4. CLASSFICATION OF ADMISSION
					</div>
					<!-- 'action'=>'/student1/create', -->
					<div class="panel-body">
						<?php $form = ActiveForm::begin(['id'=>'modelStudent','enableAjaxValidation'=>true,'validationUrl'=>'/admit-student/validate-student','options'=>['onsubmit'=>'submitStudentInfo();return false;']]); ?>
						<div class="container-fluid">
							<div class="panel panel-default">
								<div class="panel-heading" style="background-color: #8D0;">
									4.1. I am Applying for admission for:
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-6">
											<?php  echo Html::activeHiddenInput($modelStudent, "std_user_relId",['class'=>'std_user_relId']);
											?>
											<?php  echo Html::activeHiddenInput($modelStudent, "std_emergency_contact_relId",['class'=>'std_emergency_contact_relId']);
											?>
											<?php  echo Html::activeHiddenInput($modelStudent, "st_educ_info_relId",['class'=>'st_educ_info_relId']);
											?>
											<?= $form->field($modelStudent, 'std_admission_relId')->dropdownList(
						                          ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
						                          [
						                          'prompt'=>'Select admission.',
						                      ])->label('Admission') ?>

										</div>
										<div class="col-md-6">
											<?= $form->field($modelStudent, 'std_prog_relId')->dropdownList(
						                          ArrayHelper::map(\backend\models\Program::find()->where(['program_name'=>'Degree'])->all(),'id','program_name'),
						                          [
						                          'prompt'=>'Select Program.',
						                      ])->label('Program') ?>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" style="background-color: #8D0;">
									4.2. Student Type, and Marital Status
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-6">
											<?= $form->field($modelStudent, 'std_schlrshpType_relId')->dropDownList(
					                                ArrayHelper::map(
					                                        backend\models\ScholarshipType::find()->all(), 'id', 'schType_name'
					                                ),
					                                ['prompt' => 'Select Sponsorship Type', ]
					                        )?>
										</div>
										<div class="col-md-6">
											<?= $form->field($modelStudent, 'std_marital_status')->radioList(
						                            [
						                                'SINGLE'=>' Single ',
						                                'MARRIED'=>' Married ',
						                                'Other'=>' Other ',
						                            ],
						                            [
						                                'title'=>'Select Marital Status',
						                            ]
						                        )->label('Marital Status')?>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" style="background-color: #8D0;">
									4.3. Department Choice<br>
									<span class="bg-info" style="font-size: 7pt; font-style: italic; font-weight: normal;">Indicate the stream you will attend in <?= Yii::$app->params['client'].', '.Yii::$app->params['college'] ?>:
										<b>N.B: </b> <span>Rate Your choice Departmentment in the space provided (1 upto 3)</span>
									</span>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-1">
											<?php  echo Html::activeHiddenInput($modelStudent, "std_deptChoice",['class'=>'std_deptChoice']);
											?>
											<?php  echo Html::activeHiddenInput($modelStudent, "std_admissionEntries",['class'=>'std_admissionEntries']);
											?>
										</div>
										<div class="col-md-8" style="font-weight: inherit; font-size: inherit;">
											<div class="container-fluid" id="dept_choice_alert">
												
											</div>	
											<div class="container-fluid" id="dept_choice_container">
												
											</div>											
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
							<div class="col-md-3">
								<button class="btn btn-success btn-md" id="gdhgfsgdfghsdgh" onclick="return false;" title="Save form">Save</button>
							</div>
							<div class="col-md-9">
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
 // echo "<div id='modalFooter' class='container-fluid'><button class='btn btn-sm btn-warning' onclick='submitUserInfo(0)'>Cancel Admission</button></div>";
 Modal::end();
 ?>
<!-- status_message -->
<!-- dept_choice_container -->
<!-- dept_choice_select -->
<?php

$this->registerJs(
	"
	breakLoop = false;
	eiId = null;
	ssas = null;
	pse = null;
	eem = null;
	address1 = null;//Place of birth
	address2 = null;//Place of current Residential
	ecId = null;//emergency contact id.
	address3 = null;//place of emergency contact..
	uid = null;//User ID
	OTP = null;
	step = 0;

	arrayChoice = {};
	checkedIndexes = [];
	checkedDepts = [];

	window.chooseDept = function(deptId, index){

		// get index of this deptId:
		deptFirst = arrayChoice[index];
		arrayChoice[index] = deptId;

		$('#dept_choice_select_'+deptFirst).val('');
		// $('#dept_choice_alert').html('<p class=\"bg-danger\">Your Department choice order has been changed.</p>');

		$('#dept_choice_select_'+deptFirst).focus();

		console.log(arrayChoice);
		// if(index == '' || deptId == ''){
		// 	// if the sent index is '', the remove i
		// 	checkedIndexes.splice($.inArray(index,checkedIndexes),1);
		// 	checkedDepts.splice($.inArray(deptId,checkedDepts),1);
		// 	return;
		// }
		// if(jQuery.inArray(index,checkedIndexes) !== -1){ // if item is already selected, remove it because user deselected it.
  //           // checkedIndexes.splice($.inArray(index,checkedIndexes),1);
  //           $('#dept_choice_alert').html('<p class=\"bg-danger\">You have already another department as your '+index+' <sup>th</sup>choice.</p>');
  //           $('#dept_choice_select_'+deptId).val('');

  //       }else{//going to add the item to the list.
  //           checkedIndexes.push(index);
  //       }

	}

	function getItemIndex(arrayList,item){
		for(i=0;arrayList<array.length;i++){
			if(arrayList[i] == item) return i;
		}
		return false;
	}
	window.submitStudentInfo = function(){
		// e.preventDefault();
		if(step == 9 && uid != null && OTP != null){

			// console.log('INNNN');

			form = $('#modelStudent');
			admEntry = JSON.stringify([eiId,address3,address2,address1,ecId,uid]);
			arrayChoice = JSON.stringify(arrayChoice);
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
                			$('#modalContent').html('<p class=\"bg-success\">Admission Successfully Completed.</p><p>Please Review Your account activation status via the code provided below. Please Keep it secreet as much as possible.</p><pre><u>OTP</u></pre> <span style=\"background-color: #333; color:white;\">'+OTP+'<span>');

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
							rollBack('modelAddress',address2);
							rollBack('modelAddress',address3);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
                		rollBack('modelUser',uid);
						rollBack('modelEC',ecId);
			    		rollBack('modelAddress',address1);
						rollBack('modelAddress',address2);
						rollBack('modelAddress',address3);
						rollBack('modelEI',eiId);
                	}
                }
			});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}
	window.submitUserInfo = function(){
		// e.preventDefault();
		if(step == 8 && ecId != null){
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
							rollBack('modelAddress',address2);
							rollBack('modelAddress',address3);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_message').html('ERROR: '+data).focus();
                		rollBack('modelEC',ecId);
                		rollBack('modelAddress',address1);
						rollBack('modelAddress',address2);
						rollBack('modelAddress',address3);
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
		if(step == 7 && address3 != null){
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
                			// submitAddress3();

                			submitUserInfo();

                		}else{
                			$('#status_status').html('Error: '+data).focus();
                			rollBack('modelAddress',address3);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_message').html('ERROR: '+data).focus();
                		rollBack('modelAddress',address3);
						rollBack('modelEI',eiId);
                	}
                }
			});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}

	window.submitAddress3 = function(){
		// e.preventDefault();
		// subit emergency contact address info
		if(step == 6 && address2 != null){
			form = $('#modelAddress3');
			// ajax Validate the form if available.....
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
                			address3 = d[1];
                			step++;
                			$('#status_message').html('Doing Well, Proceed.: '+data).focus(); 
                			$('.hidden_input_2').val(address3);
                			// Submit emergency contact info.
                			submitEmergencyContact();
                		}else{
                			$('#status_status').html('Error: '+data).focus();
                			rollBack('modelAddress',address2);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
                		rollBack('modelAddress',address2);
						rollBack('modelEI',eiId);
                	}
                }
			});
		}else{
			console.log('Expected 9 : GOT '+step);
		}
	}


	window.submitAddress2 = function(){
		// e.preventDefault();
		if(step == 5 && address1 != null){
			form = $('#modelAddress2');
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
                			address2 = d[1];     
                			$('.place_of_residence_address_relId').val(d[1]);         			
                			step++;
                			submitAddress3();
                		}else{
                			$('#status_status').html('Error: '+data).focus();
                			rollBack('modelAddress',address1);
							rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
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
		if(step == 4 && eem != null){
			form = $('#modelAddress1');
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
                			$('.place_of_birth_address_relId').val(d[1]);         			
                			step++;
                			submitAddress2();
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

	window.submitEEM = function(){
		// e.preventDefault();
		if(step == 3 && pse != null){
			form = $('#modelEEM');
			$.ajax({
				type: 'POST',
                url: '/enterance-exam-mark/create',
                data: form.serialize(),
                async: false,
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){
                			// $('#status_message').html().focus();  
                			eem = d[1];            			
                			step++;
                			submitAddress1();
                		}else{
                			$('#status_status').html(data).focus();
                			rollBack('modelEI',eiId);
                		}
                	}catch(e){
                		$('#status_status').html('ERROR: '+data).focus();
                		rollBack('modelEI',eiId);
                	}
                }
			});
		}
	}
	window.submitPSE = function(){
		// e.preventDefault();
		if(step == 2 && ssas != null){
			form = $('#modelPSE');
			$.ajax({
    			type: 'POST',
                url: '/post-secondary-education/create',
                data: form.serialize(),
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){
                			
                			pse = d[1];
                			step++;
                			submitEEM();
                		}else{
                			$('#status_status').html(data).focus();
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
    window.submitSSA = function(){
    	// e.preventDefault();
    	if(step == 1 && eiId != null){
    		form = $('#modelSSA');
    		formID = form.id;
    		action = form.action;

    		$.ajax({
    			type: 'POST',
                url: '/secondary-school-attended/create',
                data: form.serialize(),
                success: function(data){
                	try{
                		d = JSON.parse(data);
                		if(d[0] == 1){

                			ssas = d[1];
                			step++;
                			if($('#attendedPSE_id :checked').val() == 1){
            				submitPSE();
        				}else{
        					pse = 0;
                			step++;
                			submitEEM();
        				}
                			
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
	            url: '/education-information/create',
	            data: form.serialize(),
	            success: function (data) {  
	                try{
	            		d = JSON.parse(data);
	            		if(d[0] == 1){
	            			$('#status_message').html('<br>Education Inforamtion ID => '+d[1]);
	            			$('#postsecondaryeducation-pse_ei_relid, #secondaryschoolattended-ssa_ei_relid, #enteranceexammark-eem_ei_relid').val(d[1]);
	            			
	            			$('.hidden_input_1').val(d[1]);

	            			eiId = d[1];
	            			// console.log(eiId);
	            			step++;
	            			submitSSA();	
	            		}
	        		}catch(e){
	        			$('#status_status').html(data).focus();
	        			rollBack('modelEI',eiId)
	        		}
	            },
	            error: function(jqXHR, text, error){
	            	$('#status_status').html(text).focus();
	            	rollBack('modelEI',eiId)
	            }
	        });
    	}else{
    		console.log('Expected 9 : GOT '+step);
    	}
    }

    window.rollBack = function(actionId, IDS){
    	if(actionId != undefined && IDS != undefined){
    		$.ajax({
            type: 'POST',
            url: '/admit-student/roll-back',
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

    window.validate = function(form){

    	// formID = form.id;
    	// action = form.action;

    	// if(formID == 'modelEI' && eiId == null){
    	// 	EID = submitEI(form);
    	// }

    }

    // $('#gdhgfsgdfghsdgh').click(function(e){
    // 	e.preventDefault();
    // 	// submitEI();
    // 	return submit();
    // });

    $('#gdhgfsgdfghsdgh').on('click',function(e){
    	e.preventDefault();
    	if(submit_form_validate() == true){
    		// console.log('Told to go!!');
    		submitEI();
    	}else{
    		console.log('FALSE');
    		// return undefined;
    	}
    	// return true;
    	
    });
    function submit_form_validate(){
    	valid = false;
    	// valid = $('#modelUser, #modelUser, #modelStudent, #modelAddress1, #modelAddress2, #modelAddress3, #modelEC, #modelSSA, #modelPSE, #modelEEM').on('beforeSubmit',function(e){
    	// 	console.log($('#modelAddress2').yiiActiveForm('validate'));
    	// 	return true;
    	// });
    	
    	$('#modelUser').data('yiiActiveForm').submitting = true;
    	valid = $('#modelUser').yiiActiveForm('validate');

    	// console.log('MODEL_USER => '+valid);

    	$('#modelStudent').data('yiiActiveForm').submitting = true;
    	valid = $('#modelStudent').yiiActiveForm('validate');
    	// console.log('MODEL_STUDENT => '+valid);

    	$('#modelAddress1').data('yiiActiveForm').submitting = true;
    	valid = $('#modelAddress1').yiiActiveForm('validate');
    	// console.log('MODEL_ADDRESS_1 => '+valid);

    	$('#modelAddress2').data('yiiActiveForm').submitting = true;
    	valid = $('#modelAddress2').yiiActiveForm('validate');
    	// console.log('MODEL_ADDRESS_2 => '+valid);

    	$('#modelAddress3').data('yiiActiveForm').submitting = true;
    	valid = $('#modelAddress3').yiiActiveForm('validate');
    	// console.log('MODEL_ADDRESS_3 => '+valid);

    	$('#modelSSA').data('yiiActiveForm').submitting = true;
    	valid = $('#modelSSA').yiiActiveForm('validate');
    	// console.log('MODEL_SSA => '+valid);

    	if($('#attendedPSE_id :checked').val() == 1){
    		$('#modelPSE').data('yiiActiveForm').submitting = true;
    		valid = $('#modelPSE').yiiActiveForm('validate');
    		// console.log('MODEL_PSE => '+valid);
    	}

    	$('#modelEEM').data('yiiActiveForm').submitting = true;
    	valid = $('#modelEEM').yiiActiveForm('validate');
    	// console.log('MODEL_EEM => '+valid);

    	$('#modelEC').data('yiiActiveForm').submitting = true;
    	valid = $('#modelEC').yiiActiveForm('validate');
    	return (typeof valid == 'undefined');
    }


 //    $('#modelUser').on('submit',function(e){
	// 	submitUserInfo(e);
	// 	return false;
	// });

	// $('#modelStudent').on('submit',function(e){
	// 	submitStudentInfo(e);
	// 	return false;
	// });

	// $('#modelEI').on('submit',function(e){
	// 	submitEI(e);
	// 	return false;
	// });

	// $('#modelSSA').on('submit',function(e){
	// 	submitSSA(e);
	// 	return false;
	// });

	// $('#modelPSE').on('submit',function(e){
	// 	submitPSE(e);
	// 	return false;
	// });

	// $('#modelEEM').on('submit',function(e){
	// 	submitEEM(e);
	// 	return false;
	// });

	// $('#modelAddress1').on('submit',function(e){
	// 	submitAddress1(e);
	// 	return false;
	// });

	// $('#modelAddress2').on('submit',function(e){
	// 	submitAddress2(e);
	// 	return false;
	// });

	// $('#modelAddress3').on('submit',function(e){
	// 	submitAddress3(e);
	// 	return false;
	// });

	// $('#modelEC').on('submit',function(e){
	// 		submitAddress3(e);
	// 		return false;
	// 	});

    window.gettt = function(){
    	if($('#attendedPSE_id :checked').val() == 1){
    		$('#post-secondary-education').show();
    	}else{
    		$('#post-secondary-education').hide();
    	}
    }

    $.post('/department/get-depts-for-choice',{Program:'UG'}).done(function(data){
    	$('#dept_choice_container').html(data);
    	})

    $('.form-control, .form-group').css('font-size','inherit');


	",
	$this::POS_READY);
?>
