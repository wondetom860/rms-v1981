<?php
use wbraganca\dynamicform\DynamicFormWidget;
$this->title =  "Student Admission Page V.2";

// echo "<pre>";
// var_dump($models);
// die();



if (isset($models)) {
	$userObject = $models['models']['modelUser'];
	$model = $models['models']['modelStudent'];
}else{
	die("<pre class='bg-danger'>Form NOT available (2)</pre>");
}
?>

<style type="text/css">
	.form-group.required .control-label:after {content:"*";color:red;font-weight: bold;}
	
</style>
<div class="alert alert-default">
	<h2>Student Admission Form - Post-Graduate</h2>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" style="border: 1px dashed black; background-color: white;">
			<ul class="nav nav-tabs responsive" id = "profileTab" style="background-color: #9DF;">
	            <li class="active" id = "personal-tab"><a href="#personal" data-toggle="tab"><i class="fa fa-street-view"></i> <?php echo Yii::t('decirs', 'PERSONAL INFORMATION'); ?></a></li>
	            <li id = "academic-tab"><a href="#academic" data-toggle="tab"><i class="fa fa-graduation-cap"></i> <?php echo Yii::t('decirs', 'ACADEMIC'); ?></a></li>

	            <li id = "education-tab"><a href="#education" data-toggle="tab"><i class="fa fa-pencil"></i> <?php echo Yii::t('decirs', 'EDUCATIONAL BACKGROUND'); ?></a></li>

	            <li id = "guardians-tab"><a href="#guardians" data-toggle="tab"><i class="fa fa-user"></i> <?php echo Yii::t('decirs', 'EMERGENCY CONTACT'); ?></a></li>

	            <li id = "submit-tab"><a href="#submit" data-toggle="tab"><i class="fa fa-file-text"></i> <?php echo Yii::t('decirs', 'Submit Form'); ?></a></li>
	        </ul>

	        <div id='content' class="tab-content responsive">
	            <div class="tab-pane active" id="personal">
	            	<hr>
	                <?= $this->render('_user/create', ['sessionForm' => $models]) ?> 
	            </div>

	            <div class="tab-pane" id="academic">
	            	<hr>
	                <?= $this->render('_student/create', ['sessionForm' => $models]) ?> 
	            </div>

	            <div class="tab-pane" id="education">
	            	<hr>
	                <?= $this->render('_educationInfo/_master', ['sessionForm' => $models]) ?> 
	            </div>

	            <div class="tab-pane" id="guardians">
	            	<hr>
	                <?= $this->render('_emergencyContact/_ecMaster', ['sessionForm' => $models]) ?> 
	            </div>

	            <div class="tab-pane" id="submit">
	            	<hr>
	                <div class="container-fluid" id="formSubmissionReport">
	                	<table class="table table-striped table-bordered table-hover table-xs table-condensed">
	                		<thead style="background-color: #8D0;">
	                			<th>#</th>
	                			<th colspan="3">Sub-Form Name</th>
	                			<th>Status</th>
	                		</thead>
	                		<tbody>
	                			<tr>
	                				<td>1</td>
		                			<td colspan="3">Personal Inforamtion</td>
		                			<td id="modelUser_r" class="warning">Not submitted</td>
	                			</tr>
	                			<tr>
	                				<td>2</td>
		                			<td colspan="3">Academic Inforamtion</td>
		                			<td id="modelStudent_r" class="warning">Not submitted</td>
	                			</tr>
	                			<tr>
	                				<td rowspan="3">3</td>
		                			<td colspan="3" class="warning">Education Background Inforamtion</td>
		                			<td> - </td>
	                			</tr>
	                			<tr>
	                				<td>3.1</td>
		                			<td colspan="2">Secondary Schools Attended</td>
		                			<td id="modelSSA_r" class="warning">Not submitted</td>
	                			</tr>	
	                			<tr>
	                				<td>3.2</td>
		                			<td colspan="2">Post Secondary Schools Attended</td>
		                			<td id="modelPSE_r" class="warning">Not submitted</td>
	                			</tr>	
	                			<tr>
	                				<td rowspan="3">4</td>
		                			<td colspan="3" class="warning">Emergency Contact</td>
		                			<td> - </td>
	                			</tr>  
	                			<tr>
	                				<td>4.1</td>
		                			<td colspan="2">EC. Personal Inforamtion</td>
		                			<td id="modelEC_r" class="warning">Not submitted</td>
	                			</tr> 
	                			<tr>
	                				<td>4.2</td>
		                			<td colspan="2">EC. Address Information</td>
		                			<td id="modelAddressEC_r" class="warning">Not submitted</td>
	                			</tr>               			
	                		</tbody>
	                	</table>
	                </div>
	                <hr>
	                <div class="row">
	                	<div class="col-md-12">
	                		<div class="container">
	                			<center>
	                				<button disabled="true" class="btn btn-primary" id="wonde_tom_8472" onclick="submitFormAll();">Submit Form</button>
	                			</center>
	                		</div>
	                	</div>
	                	<div class="col-md-12">
	                		<div class="container-fluid" id="applicationSubmissionStatus">
	                			
	                		</div>
	                	</div>
	                </div>
	            </div>
	        </div>
		</div>
	</div>
</div>

<script type="text/javascript">
	readyForms = [];
	allForms = ['modelUser','modelStudent','modelEI','modelEEM','modelSSA','modelEC','modelAddressBirth','modelAddressRedencial','modelPSSE','modelAddressEC'];
	requiredForms = ['modelUser','modelStudent','modelEI','modelEEM','modelSSA','modelEC','modelAddressBirth','modelAddressRedencial'];

	window.submitFormAll = function(){
		stringifiedForms = {};
		completed = true;
		// jQuery.each(allForms,function(index,value){
		// 	console.log(value);
		// });
		jQuery.each(readyForms,function(index,value){
			// if the form is required and NOT completed yet, arise error.
			if ((jQuery.inArray(value,requiredForms)  !== -1) && (jQuery.inArray(value,readyForms)  == -1)) {
				$("#wonde_tom_8472").prop('disabled',true);
				$("#applicationSubmissionStatus").html("<span class='bg-danger'>Required Fields remaining..."+value+"</sapn>");
				completed = false;
				// return;
			}

			form = $('#'+value).serialize();
			stringifiedForms[value] = form;
			// console.log(value);
		});

		if (completed) {
			if (confirm('You want to submit the form?')) {
				$("#applicationSubmissionStatus").html('Submitting ... ');
				$.post('/admit-student/self-admission-pg',{sessionForm : stringifiedForms }).done(function(data){
					$("#applicationSubmissionStatus").html(data);
				});
			}
		}
		// console.log(stringifiedForms);
		return false;

	}

	window.refreshFrom = function(){
		if(confirm("Collected OTP and ready for completeing the opration?")){
			window.location.href = window.location;
		}
	}
	window.submitMaster = function(formId){
		repeated = false;
		if(jQuery.inArray(formId,readyForms) !== -1)//incase if updated the content,
		{
			repeated = true;
			readyForms.splice($.inArray(formId,readyForms),1); 
		}
		
		readyForms.push(formId);

		if(!repeated){
			form = $('#'+formId).serialize();
			// console.log('#'+formId+'_r');
			$('#'+formId+'_r').removeClass('warning');
			$('#'+formId+'_r').addClass('success').html('Filled');
        	// $("#formSubmissionReport").append("<hr><pre>"+form+'</pre>');
		}
		$("#wonde_tom_8472").prop('disabled',false);
        return 1;
	}
</script>
