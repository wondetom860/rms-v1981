<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Application Form';
$this->params['breadcrumbs'][] = $this->title;
?><br>
<div class="container-fluid">

	<div class="panel panel-info">
		<div class="panel-body"  style="border: 1px dashed black;" id="printableDiv">
			<div class="container-fluid" style="padding: 5px 50px;">
				<div class="row">
					<table class="table table-condensed" style="width: 100%; text-align: center;">
						<tbody>
							<tr>
								<td><?php echo '<img height="90" with="90" class="pull-right" alt="DEC Logo" src="/uploads/decLogo.png">' ?></td>
								<td>
									<h4 style="font-weight: bold;">DEFENCE UNIVERSITY COLLEGE<br>OFFICE OF REGISTRAR<br>DEBREZEIT</h4>
									<p>P.O.Box 1041, Tel. (01)33-84-00</p>
									<h5 style="text-decoration: underline; font-weight: bold;">Application for Admission into the Post Graduate Program</h5>
								</td>
								<td><?php echo '<img height="100" with="85" alt="Photo" class="pull-left" src="/uploads'.'/'.$modelUser->photo_num.'">';
							echo "<p style='font-size:5pt;'>{$modelUser->OTP}</p>"; ?></td>
							</tr>
						</tbody>
					</table>
				</div><hr>
				<div class="row">
					<div class="col-md-12">
						<table class="table table-striped table-condensed table-bordered table-hover">
							  <tbody>
								<tr  style="background-color: #ace;">
									<td>1.</td>						
									<td nowrap="nowrap" colspan="3">Modularity of the study &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?= \backend\models\Admission::findOne($modelStudent->std_admission_relId)->admission_name . ' '.\backend\models\Program::findOne($modelStudent->std_prog_relId)->program_name ?></b></td>	
								</tr>
								<tr>
									<td style="vertical-align: middle;">2</td>
									<td colspan="2" style="vertical-align: middle;">Department</td>
									<td><b><?php

									if (!is_null($modelStudent->std_dept_relId)) {
										echo "{$modelStudent->stdDeptRel->dept_name}";
									}else{
										echo "<i>Not Set.</i>";
									}

								?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">&nbsp;</td>
									<td colspan="2" style="vertical-align: middle;">Specialization</td>
									<td><b><?php

									if (!is_null($modelStudent->std_spec_relId)) {
										echo "{$modelStudent->stdSpecRel->spec_name}";
									}else{
										echo "<i>Not Set.</i>";
									}

								?></b></td>
								</tr>
								<tr  style="background-color: #ace;">
									<td>3.</td>
									<td nowrap="nowrap" colspan="3">Personal Information</td>						
							   </tr>
								<tr>
									<td>3.1</td>
									<td colspan="2">Reg. Id: </td>
									<td><b><?=  $modelStudent->std_regId ?></b></td>
								</tr>
								<tr>
									<td>3.2</td>
									<td colspan="2">Full Name : </td>
									<td><b><?= $modelUser->first_name.' '.$modelUser->middle_name. ' ' .$modelUser->last_name ?></b></td>
								</tr>
								<tr>
									<td>3.3</td>
									<td colspan="2">Gender : </td>
									<td><b><?= $modelUser->gender ?></b></td>								
								</tr>
								
								<tr>
									<td style="vertical-align: middle;">3.4</td>
									<td colspan="2" style="vertical-align: middle;">Date of Birth: </td>
									<td><b><?= date_format(date_create($modelUser->dob),'d') . ' ' . date_format(date_create($modelUser->dob),'M') . ' ' . date_format(date_create($modelUser->dob),'Y') ; ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">3.5</td>
									<td colspan="2" style="vertical-align: middle;">Student Type: </td>
									<td><b><?=  $modelStudent->std_type ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">3.6</td>
									<td colspan="2" style="vertical-align: middle;">Sponsorship: </td>
									<td><b><?php echo is_null($modelStudent->std_schlrshpType_relId)?'NOT SET':\backend\models\ScholarshipType::findOne($modelStudent->std_schlrshpType_relId)->schType_name ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">4</td>
									<td colspan="2" style="vertical-align: middle;">Emergency Contact / Reference </td>
									<td><b><?php 
									if (is_null($modelEC)) {
										echo "<i>Not Set.</i>";
									} else{
										if (is_null($modelAddressEC)) {
											echo "<i>Not Set.</i>";
										}else{
											echo "<table class='table table-condensed table-striped' style='font-size:9pt; width:100%;'>";
											echo "<thead style='background-color:#8D0;'>
												<th>Name</th>
												<th>RelationShip</th>
												<th>Telephone</th>
												<th>Kebele</th>
												<th>Town</th>
												<th>Woreda</th>
												<th>Zone</th>
												<th>Region</th>
												</thead><tbody>";
											// foreach ($modelSSA as $key) {
												// $gg = intval(json_decode($key->ssa_gradeLevels)[0]);
											$rr = \backend\models\Region::findOne($modelAddressEC->address_region_relId)->region_name; 
											echo "<tr style='font-weight:normal;'>
												<td>{$modelEC->ec_first_name} {$modelEC->ec_last_name} </td>
												<td>{$modelEC->ec_relationShip}</td>
												<td>{$modelEC->ec_phone_mobile}</td>
												<td>{$modelAddressEC->address_kebele} </td>
												<td>{$modelAddressEC->address_town} </td>
												<td>{$modelAddressEC->address_woreda} </td>
												<td>{$modelAddressEC->address_zone} </td>
												<td>{$rr}</td>
												</tr>";
											// }
											echo "</tbody></table>";
										}
									} ?></b></td>
								</tr>
								<tr  style="background-color: #ace;">
									<td>5.</td>
									<td nowrap="nowrap" colspan="3">Educational Background Information</td>					
							   </tr>

								<tr>
									<td style="vertical-align: middle;">5.1</td>
									<td colspan="2" style="vertical-align: middle;">Secodary School(s) attended:</td>
									<td><b><?php

										if (is_null($modelSSA)) {
											echo "<i>Not Set.</i>";
										}else{
											echo "<b><h5>YES</h5></b>";
											echo "<table class='table table-condensed table-striped' style='font-size:9pt; width:100%;'>";
											echo "<thead style='background-color:#8D0'>
													<th>Name of Institute</th>
													<th>Location</th>
													<th>Deploma awarded</th>
													<th>Date of award</th>
												</thead><tbody>";
											foreach ($modelPSE as $key) {
												echo "<tr>
													<td> {$key->pgpss_institute_name} </td>
													<td> {$key->pgpss_location} </td>
													<td> {$key->pgpss_deploma_awarded}</td>
													<td> {$key->pgpss_date_of_award}</td>
												</tr>";
											}
											echo "</tbody></table>";
										}

									?></b>
									</td>
								</tr>
								
								<tr>
									<td style="vertical-align: middle;">5.2</td>
									<td colspan="2" style="vertical-align: middle;">Post Secondary Education: </td>
									<td><i>Attended in any post-secondary educational Education(s) [University or college level] ? </i> <?php  
									if (!($modelPSE && count($modelPSE) > 0)) {
										echo "<i>Not Attended.</i>";
									}else{
										echo "<b><h5>YES</h5></b>";
										echo "<table class='table table-condensed table-striped' style='font-size:9pt; width:100%;'>";
										echo "<thead style='background-color:#8D0'>
												<th>Name of Institute</th>
												<th>Country</th>
												<th>Year (from)</th>
												<th>Year (to)</th>
												<th>Credit Eraned</th>
												<th>CGPA</th>
												<th>Certificate</th>
											</thead><tbody>";
										foreach ($modelPSE as $key) {
											// $gg = intval(json_decode($key->eem_gradeLevels)[0]);
											// $cc = \backend\models\Nationality::findOne($key->pse_nationality_relId)->name;
											echo "<tr>
												<td> {$key->pse_institute_name} </td>
												<td> {$cc} </td>
												<td> {$key->pse_from_year} </td>
												<td> {$key->pse_to_year}</td>
												<td> {$key->pse_credit_earned}</td>
												<td> {$key->pse_CGPA_obtained}</td>
												<td> {$key->pse_certificate_earned}</td>
											</tr>";
										}
										echo "</tbody></table>";
									} ?></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">5.3</td>
									<td colspan="2" style="vertical-align: middle;">UnderGraduate University</td>
									<td><b><?php

									if ($modelEI && count($modelEI) > 0) {
										echo "University Name: <u style='font-weight:normal;'>{$modelEI->pgei_UG_university_name}</u>";
										echo "&nbsp;&nbsp;&nbsp;CGPA: <u style='font-weight:normal;'>".sprintf("%.02f",$modelEI->pgei_CGPA)."</u>";
										echo "&nbsp;&nbsp;&nbsp;Field Of Study: <u style='font-weight:normal;'>{$modelEI->pgei_field_of_study}</u>";
										echo "&nbsp;&nbsp;&nbsp;Major: <u style='font-weight:normal;'>{$modelEI->pgei_major}</u>";
										echo "&nbsp;&nbsp;&nbsp;Minor: <u style='font-weight:normal;'>{$modelEI->pgei_minor}</u>";
									}else{
										echo "<i>Not Set.</i>";
									}

								?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">6</td>
									<td colspan="2" style="vertical-align: middle;">Work Experience</td>
									<td><b><?php

									if ($modelEI && count($modelEI) > 0) {
										try {
											if (!is_null($modelEI->pgei_awards)) {
												$filehandler = file_get_contents($modelEI->pgei_work_experience);
												echo "<p class='info' style='font-size:9pt; font-style:normal;'>{$filehandler}</p>";
											}else{
												echo "<i>Not Available.</i>";
											}
										} catch (Exception $e) {
											echo "<i>Not Available.</i>";
										}
										
									}else{
										echo "<i>Not Set.</i>";
									}

								?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">7</td>
									<td colspan="2" style="vertical-align: middle;">Research Work</td>
									<td><b><?php

									if ($modelEI && count($modelEI) > 0) {
										try {
											if (!is_null($modelEI->pgei_reserches)) {
												$filehandler = file_get_contents($modelEI->pgei_reserches);
												echo "<p class='info' style='font-size:9pt; font-style:normal;'>{$filehandler}</p>";
											}else{
												echo "<i>Not Available.</i>";
											}
										} catch (\Exception $e) {
											echo "<i>Not Available.</i>";
										}
										
									}else{
										echo "<i>Not Set.</i>";
									}

								?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">8</td>
									<td colspan="2" style="vertical-align: middle;">Awards</td>
									<td><b><?php

									if ($modelEI && count($modelEI) > 0) {
										try {
											if (!is_null($modelEI->pgei_awards)) {
												$filehandler = file_get_contents($modelEI->pgei_awards);
												echo "<p class='info' style='font-size:9pt; font-style:normal;'>{$filehandler}</p>";
											}else{
												echo "<i>Not Available.</i>";
											}
										} catch (Exception $e) {
											echo "<i>Not Set.</i>";
										}
										
									}else{
										echo "<i>Not Set.</i>";
									}

								?></b></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align: right; ">
										<?= isset($_GET['lPrint'])? '<p style="border-top: 1px solid black; font-style:italic; font-size:9pt;">Report Generated on '.date('r').'</p>' : '<button class="btn btn-primary pull-right btn-xs non-print" onclick="printForm('.$modelUser->id.')">Print</button>'  ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
<!-- ressult_container -->

<?php
$this->registerJs(
	"

	// pass ID to this function
	window.printForm = function(id){
		$('.non-print').hide();
		 myWindow = window.open(
          '/admit-student/admission-form-report?id='+id+'&lPrint='+1, 
          'Print Students Admission Report', 
          'height=600,width=600');

        myWindow.print();
        // myWindow.close();
        return false;
	}
	userId = null;

	window.loadImagePRE = function(userId1){
		userId = userId1;
		console.log(userId);
		// get user uploadeed photo.
		$('#user1-id_for_file').val(userId1);
		$.post('/admit-student/get-user-image',{userId:userId1}).done(function(data){
			$('#user_image').attr('src','/uploads/'+data);
			$('#profileImageContainer').show();
		});
	}
	window.searchUser = function(){
		userId2 = $('#id_input').val();
		if( userId2 == 'undefined' || userId2 == '' || userId2 == ' '){
			alert('Input empty, Please enter the one.');
			$('#id_input').focus();
		}else{
			$('#ressult_container').html('Searching...');
			$.post('/admit-student/get-user-by-id',{userId:userId2}).done(function(data){
				$('#ressult_container').html(data);
			});
		}
	}
	",
	$this::POS_READY);
?>