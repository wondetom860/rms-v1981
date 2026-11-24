<?php 
	use yii\helpers\Html;
	$adminUser = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
	$modelAddressB = $modelUser->place_of_birth_address_relId?$modelUser->placeOfBirthAddressRel:NULL;
	$modelAddressC = $modelUser->place_of_residence_address_relId?$modelUser->placeOfResidenceAddressRel:NULL;
?>

<div class="row">
  <div class="col-xs-12">
	<h2 class="page-header">	
	<i class="fa fa-info-circle"></i> <?php echo Yii::t('rms', 'Address Info'); ?>
	</h2>
  </div><!-- /.col -->
</div>

<!---Start Current Address Block--->
<div class="row">
  <div class="col-xs-12">
	<h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">	
		<?= Html::encode(Yii::t('rms', 'Place of Birth')) ?>
		<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right'?>">
			<?php if(Yii::$app->user->can("Admin") && $modelAddressB) { ?>
			    <?= Html::a('<i class="fa fa-pencil-square-o"></i>'.Yii::t('rms', 'Edit'), ['/address-info/update', 'id' => $modelAddressB->id, 'tab' => 'address'], ['class' => 'btn btn-primary btn-sm', 'id' => 'update-data']) ?>
			<?php } ?> 
		</div>
	</h4>
  </div><!-- /.col -->
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressB)?$modelAddressB->getAttributeLabel('address_country_relId'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressB) ? $modelAddressB->addressCountryRel->name : "" ?></div>
  </div>
</div>

 <div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressB)?$modelAddressB->getAttributeLabel('address_region_relId'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressB) ? $modelAddressB->addressRegionRel->region_name : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressB)?$modelAddressB->getAttributeLabel('address_woreda'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressB) ? $modelAddressB->address_woreda : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressB)?$modelAddressB->getAttributeLabel('address_zone'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressB) ? $modelAddressB->address_zone : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressB)?$modelAddressB->getAttributeLabel('address_town'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressB) ? $modelAddressB->address_town : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressB)?$modelAddressB->getAttributeLabel('address_kebele'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressB) ? $modelAddressB->address_kebele : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressB)?$modelAddressB->getAttributeLabel('address_hourse_no'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressB) ? $modelAddressB->address_hourse_no : "" ?></div>
  </div>
</div>

<!---Start Permenant Address Block--->
<div class="row">
  <div class="col-xs-12">
	<h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">	
		<?= Html::encode(Yii::t('rms', 'Current Residence Address')) ?>
		<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right'?>">
			<?php if(Yii::$app->user->can("Admin") && $modelAddressC) { ?>
			    <?= Html::a('<i class="fa fa-pencil-square-o"></i>'.Yii::t('rms', 'Edit'), ['/address-info/update', 'id' => $modelAddressC->id, 'tab' => 'address'], ['class' => 'btn btn-primary btn-sm', 'id' => 'update-data']) ?>
			<?php } ?> 
		</div>
	</h4>
  </div><!-- /.col -->
</div>


<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressC)?$modelAddressC->getAttributeLabel('address_country_relId'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressC) ? $modelAddressC->addressCountryRel->name : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressC)?$modelAddressC->getAttributeLabel('address_region_relId'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressC) ? $modelAddressC->addressRegionRel->region_name : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressC)?$modelAddressC->getAttributeLabel('address_woreda'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressC) ? $modelAddressC->address_woreda : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressC)?$modelAddressC->getAttributeLabel('address_zone'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressC) ? $modelAddressC->address_zone : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressC)?$modelAddressC->getAttributeLabel('address_town'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressC) ? $modelAddressC->address_town : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressC)?$modelAddressC->getAttributeLabel('address_kebele'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressC) ? $modelAddressC->address_kebele : "" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= ($modelAddressC)?$modelAddressC->getAttributeLabel('address_hourse_no'):'' ?></div>
	<div class="col-md-8 col-xs-8"><?=  ($modelAddressC) ? $modelAddressC->address_hourse_no : "" ?></div>
  </div>
</div>