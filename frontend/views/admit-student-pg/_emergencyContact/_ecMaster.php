<?php

// $models = Yii::$app->getFlash('applicationForm');
if (isset($sessionForm)) {
	// $modelEC = $sessionForm['models']['modelEC'];
	// $modelECA = $models['models']['modelAddressEC'];//Emergenct contact address
}else{
	echo "<pre class='bg-danger'>Form NOT available</pre>";
	return;
}

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" style="border: 1px dashed black; background-color: white; font-size: 9pt; margin: 5px;">
			<ul class="nav nav-tabs responsive" id = "profileTab" style="background-color: #f39c12; font-weight: bold;">

	            <li id = "ec_personal-tab">
	            	<a href="#ec_personal" data-toggle="tab">
	            		<i class="fa fa-graduation-cap"></i> 
	            		<?php echo Yii::t('decirs', 'EMERGENCY CONTACT - PERSONAL INFO'); ?>
	            	</a>
	            </li>

	            <li id = "ec_address-tab"><a href="#ec_address" data-toggle="tab"><i class="fa fa-exam"></i> <?php echo Yii::t('decirs', 'EMERGENCY CONTACT - ADDRESS INFO'); ?></a></li>
	        </ul>

	        <div id='content' class="tab-content responsive">

	            <div class="tab-pane" id="ec_personal">
	            	<hr>
	                <?= $this->render('_ecPersonal/create', ['sessionForm' => $sessionForm]) ?> 
	            </div>

	            <div class="tab-pane" id="ec_address">
	            	<hr>
	                <?= $this->render('_ecAddress/create', ['sessionForm' => $sessionForm]) ?> 
	            </div>

	        </div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
</script>