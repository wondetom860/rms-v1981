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
			<div class="container-fluid" style="padding: 5px 20px;">
				<div class="row" style="border-bottom: 1px solid black;">
					<table class="table table-condensed table-bordered" style="width: 100%; text-align: center;">
						<tbody style="border-color: black;">
							<tr>
								<td><?php echo '<img height="90" with="90" class="pull-right" alt="Logo" src="/uploads/'.CLIENT_LOGO.'">' ?></td>
								<td>
									<h5 style="font-weight: bold;"><?= strtoupper(Yii::$app->params['client'] ).", ". strtoupper(Yii::$app->params['college'] ) ?><br>OFFICE OF THE REGISTRAR<br><?= signature['town'] ?></h5>
									<p>P.O.Box <?= signature['pobx'] ?>, Tel. <?= signature['tele'] ?></p>
									<h6 style="text-decoration: underline; font-weight: bold;">ADMISSION APPLICATION FORM</h6>
								</td>
								<td>
									<center>
										<?php 
										echo '<figure><img height="100" with="85" alt="Photo" src="/uploads'.'/'.$modelUser->photo_num.'">
											<figcaption style="font-size:6pt;">'. $modelUser->OTP .'</figcaption></figure>';
										?>
									</center>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row">
					<div class="col-md-12 col-xs-12 col-lg-12">
						<p style="font-size: 9pt;">
							This form, completed and accompanied by all necessary educational documents, must be returned to the Registrar's Office on or before the specified date.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table class="table table-striped table-condensed table-bordered table-hover">
							  <tbody>
								<tr  style="background-color: #ace;">
									<td>1.</td>						
									<td nowrap="nowrap" colspan="3">Classification of Admission &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?= \backend\models\Admission::findOne($modelStudent->std_admission_relId)->admission_name . ' '.\backend\models\Program::findOne($modelStudent->std_prog_relId)->program_name ?></b></td>	
								</tr>
								<tr  style="background-color: #ace;">
									<td>2.</td>
									<td nowrap="nowrap" colspan="3">Personal Information</td>						
							   </tr>
								<tr>
									<td>2.1</td>
									<td colspan="2">Reg. Id: </td>
									<td><b><?=  $modelStudent->std_regId ?></b></td>
								</tr>
								<tr>
									<td>2.2</td>
									<td colspan="2">Full Name : </td>
									<td><b><?= $modelUser->first_name.' '.$modelUser->middle_name. ' ' .$modelUser->last_name ?></b></td>
								</tr>
								<tr>
									<td>2.3</td>
									<td colspan="2">Gender : </td>
									<td><b><?= $modelUser->gender ?></b></td>								
								</tr>
								
								<tr>
									<td style="vertical-align: middle;">2.4</td>
									<td colspan="2" style="vertical-align: middle;">Place Of Birth</td>
									<td><b><?php 
									if (is_null($modelAddressBirth)) {
										echo "<i>Not Set.</i>";
										} 
									else{
										// $plOfBrth = $key->place_of_birth_address_relId?($key->placeOfBirthAddressRel->addressRegionRel?$key->placeOfBirthAddressRel->addressRegionRel->region_name:'-'):'-';
										$rr = $modelAddressBirth->address_region_relId?\backend\models\Region::findOne($modelAddressBirth->address_region_relId)->region_name:'-'; 
										$ww = $modelAddressBirth?$modelAddressBirth->address_woreda:'-'; 
										echo "Town: <u>{$modelAddressBirth->address_town}</u>&nbsp;&nbsp; Wereda: <u>{$ww}</u>&nbsp;&nbsp; Region: <u>{$rr}</u>";
										} ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">2.5</td>
									<td colspan="2" style="vertical-align: middle;">Date of Birth: </td>
									<td><b><?= date_format(date_create($modelUser->dob),'d') . ' ' . date_format(date_create($modelUser->dob),'M') . ' ' . date_format(date_create($modelUser->dob),'Y') ; ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">2.6</td>
									<td colspan="2" style="vertical-align: middle;">Cur. Res. Address: </td>
									<td><b><?php 
									if (is_null($modelAddressRedencial)) {
										echo "<i>Not Set.</i>";
									} else{
										$rr = \backend\models\Region::findOne($modelAddressRedencial->address_region_relId)->region_name; 
										echo "House No: <u>{$modelAddressRedencial->address_hourse_no}</u>&nbsp&nbsp&nbsp;
										  Kebele: <u>{$modelAddressRedencial->address_kebele}</u>&nbsp;
										  Town: <u>{$modelAddressRedencial->address_town}</u>&nbsp;
										  Wereda: <u>{$modelAddressRedencial->address_woreda}</u>&nbsp; 
										  Region: <u>{$rr}</u>";
									} ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">2.7</td>
									<td colspan="2" style="vertical-align: middle;">Marital Status: </td>
									<td><b><?=   $modelStudent->std_marital_status?$modelStudent->std_marital_status:'<i>Not Set.</i>' ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">2.8</td>
									<td colspan="2" style="vertical-align: middle;">Student Type: </td>
									<td><b><?=  $modelStudent->std_type ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">2.9</td>
									<td colspan="2" style="vertical-align: middle;">Sponsorship: </td>
									<td><b><?php echo is_null($modelStudent->std_schlrshpType_relId)?'NOT SET':\backend\models\ScholarshipType::findOne($modelStudent->std_schlrshpType_relId)->schType_name ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">2.91</td>
									<td colspan="2" style="vertical-align: middle;">Emergency Contact: </td>
									<td><b><?php 
									if (is_null($modelEC)) {
										echo "<i>Not Set.</i>";
									} else{
										if (is_null($modelAddressEC)) {
											echo "<i>Not Set.</i>";
										}else{
											echo "<table class='table table-condensed table-striped table-bordered' style='font-size:9pt; width:100%;'>";
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
									<td>3.</td>
									<td nowrap="nowrap" colspan="3">Educational Information</td>					
							   </tr>

								<tr>
									<td style="vertical-align: middle;">3.1</td>
									<td colspan="2" style="vertical-align: middle;">Secodary School(s) attended:</td>
									<td><b><?php

										if (is_null($modelSSA)) {
											echo "<i>Not Set.</i>";
										}else{
											echo "<table class='table table-condensed table-striped table-bordered' style='font-size:9pt; width:100%;'>";
											echo "<thead style='background-color:#8D0'><th>School Name</th><th>Town</th><th>Year</th><th>Grade Level</th></thead><tbody>";
											foreach ($modelSSA as $key) {
												$fr = $key->ssa_gradeLevels;
												$gg = is_numeric($fr)?$fr."<sup>th</sup>":$fr;
												echo "<tr><td> {$key->ssa_school_name} </td><td> {$key->ssa_town} </td><td> {$key->ssa_year} </td><td> {$gg} </td></tr>";
											}
											echo "</tbody></table>";
										}

									?></b>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">3.2</td>
									<td colspan="2" style="vertical-align: middle;">Ethiopian Higher Education Enterance Examination (EHEEE).</td>
									<td><?php

									if (is_null($modelEEM)) {
											echo "<i>Not Set.</i>";
										}else{
											echo "<table class='table table-condensed table-striped table-bordered' style='font-size:9pt; width:100%;'>";
											echo "<thead style='background-color:#8D0'>
													<th>Subject</th>
													<th>Reference N0.</th>
													<th>Year (E.C)</th>
													<th>Grade (Mark)</th>
												</thead><tbody>";
											foreach ($modelEEM as $key) {
												// $gg = intval(json_decode($key->eem_gradeLevels)[0]);
												echo "<tr>
													<td> {$key->eem_subject} </td>
													<td> {$key->eem_reference_number} </td>
													<td> {$key->eem_year} </td>
													<td> {$key->eem_markObtained}</td>
												</tr>";
											}
											echo "</tbody></table>";
										}

									?></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">3.3</td>
									<td colspan="2" style="vertical-align: middle;">College Ennterance Examination Grade:</td>
									<td><b><?php echo is_null($modelEI)?'NOT SET':$modelEI->ei_enteranceMarkPoint ?></b></td>
								</tr>
								<tr>
									<td style="vertical-align: middle;">3.4</td>
									<td colspan="2" style="vertical-align: middle;">Post Secondary Education: </td>
									<td><i>Attended in any post-secondary educational Education(s) [University or college level] ? </i> <?php  
									if (!($modelPSE && count($modelPSE) > 0)) {
										echo "<i>Not Attended.</i>";
									}else{
										echo "<b><h5>YES</h5></b>";
										echo "<table class='table table-condensed table-striped table-bordered' style='font-size:9pt; width:100%;'>";
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
											$cc = \backend\models\Nationality::findOne($key->pse_nationality_relId)->name;
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
	
	<!-- www.mint.gov.et -->
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