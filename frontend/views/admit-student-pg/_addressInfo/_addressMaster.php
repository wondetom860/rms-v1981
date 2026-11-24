<?php

// $models = Yii::$app->getFlash('applicationForm');
if (isset($sessionForm)) {
	// $modelAddresBirth = $models['models']['modelAddressBirth'];
	// $modelAddresResidential= $models['models']['modelAddressRedencial'];
	// $userObject = $models['models']['modelUser'];
	// $model = $models['models']['modelStudent'];
}else{
	// echo "<h3>Address Info</h3>";
	echo "<pre class='bg-danger'>Form NOT available</pre>";
	return;
}
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" style="border: 1px dashed black; background-color: white; font-size: 9pt;">
			<ul class="nav nav-tabs responsive" id = "profileTab" style="background-color: #f39c12; font-weight: bold;">

	            <li id = "addressBirth-tab"><a href="#addressBirth" data-toggle="tab"><i class="fa fa-graduation-cap"></i> <?php echo Yii::t('decirs', 'ADDRESS OF BIRTH'); ?></a></li>

	            <li id = "addressResidential-tab"><a href="#addressResidential" data-toggle="tab"><i class="fa fa-exam"></i> <?php echo Yii::t('decirs', 'ADDRESS OF CURRENT RESIDENTIAL'); ?></a></li>
	        </ul>

	        <div id='content' class="tab-content responsive">

	            <div class="tab-pane" id="addressBirth">
	            	<hr>
	                <?= $this->render('_addressBirth/create', ['sessionForm' => $sessionForm]) ?> 
	            </div>

	            <div class="tab-pane" id="addressResidential">
	            	<hr>
	                <?= $this->render('_addressResidential/create', ['sessionForm' => $sessionForm]) ?> 
	            </div>
	        </div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#addressinfo-address_country_relid').focus();
</script>