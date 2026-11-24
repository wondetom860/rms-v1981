<?php
use wbraganca\dynamicform\DynamicFormWidget;
// $models = Yii::$app->getFlash('applicationForm');
if (isset($sessionForm)) {
	$models = &$sessionForm;
}else{
	echo "<pre class='bg-danger'>Form NOT available (2)</pre>";
	return;
}
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" style="border: 1px dashed black; background-color: white; font-size: 9pt; margin: 5px;">
			<ul class="nav nav-tabs responsive" id = "profileTab" style="background-color: #f39c12; font-weight: bold; color: white;">

				<li id = "secondarySchools-tab"><a href="#secondarySchools" data-toggle="tab"><i class="fa fa-graduation-cap"></i> <?php echo Yii::t('decirs', 'SECONDARY SCHOOLS ATTENDED'); ?></a></li>

	            <li id = "postSecondarySchools-tab"><a href="#postSecondarySchools" data-toggle="tab"><i class="fa fa-home"></i> <?php echo Yii::t('decirs', 'POST SECONDARY EDUCATION'); ?></a></li>

	            <li id = "educationIfo-tab"><a href="#educationIfo" data-toggle="tab"><i class="fa fa-home"></i> <?php echo Yii::t('decirs', 'EDUCATION INFORMATION'); ?></a></li>
	        </ul>

	        <div id='content' class="tab-content responsive">

	            <div class="tab-pane" id="secondarySchools">
	            	<hr>
	                <?= $this->render('_secondarySchoolsAttended/create', ['sessionForm' => $models]) ?> 
	            </div>
	            
	            <div class="tab-pane" id="educationIfo">
	            	<hr>
	                <?= $this->render('create', ['sessionForm' => $models]) ?> 
	            </div>

	            <div class="tab-pane" id="postSecondarySchools">
	            	<hr>
	                <?= $this->render('_postSecondaryEducation/create', ['sessionForm' => $models]) ?> 
	            </div>

	        </div>
		</div>
	</div>
</div>