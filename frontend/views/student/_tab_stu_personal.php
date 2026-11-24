
<?php
use yii\helpers\Html; 
$adminUser = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
?>

<div class="row">
  <div class="col-xs-12">
	<h2 class="page-header">	
	<i class="fa fa-info-circle"></i> <?php echo Yii::t('rms', 'Personal Details'); ?>
	<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right'?>">
	<?= Html::a('<i class="fa fa-pencil-square-o"></i> '.Yii::t('rms', 'Edit'), ['/user/update', 'id' => $info->id, 'tab' => 'personal'], ['class' => 'btn btn-primary btn-sm', 'id' => 'update-data']) ?>
	</div>
	</h2>
  </div><!-- /.col -->
</div>

<div class="row container-fluid" style="margin-top: 5px; border-bottom: 1px solid black;">
	<div class="col-md-12 col-xs-12 col-sm-12">
	  <div class="col-lg-12 col-sm-12 col-xs-12 no-padding edusec-bg-row">
		<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $model->getAttributeLabel('std_user_relId') ?></div>
		<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $model->getFullName() ?></div>
	  </div>
	</div>
</div>
<div  class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
	<div class="col-md-12 col-xs-12 col-sm-12">
	  <div class="col-lg-12 col-sm-12 col-xs-12 no-padding">
		<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $info->getAttributeLabel('gender') ?></div>
		<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $info->gender ?></div>
	  </div>
	</div>
</div>
<div  class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
	<div class="col-md-12 col-xs-12 col-sm-12">
	  <div class="col-lg-12 col-sm-12 col-xs-12 no-padding">
		<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $info->getAttributeLabel('eth_group_relId') ?></div>
		<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $info->ethGroupRel?"{$info->ethGroupRel->name}":'-' ?><button class="btn btn-xs btn-warning pull-right" onclick="laodUpdateForm(<?= $model->id ?>); return false;">Update</button></div>
	  </div>
	</div>
</div>
<div  class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
	<div class="col-md-12 col-xs-12 col-sm-12">
	  <div class="col-lg-12 col-sm-12 col-xs-12 no-padding">
		<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $info->getAttributeLabel('dob') ?></div>
		<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= Yii::$app->formatter->asDate($info->dob); ?></div>
	  </div>
	</div>
</div>
<div  class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
	<div class="col-md-12 col-xs-12 col-sm-12">
	  <div class="col-lg-12 col-sm-12 col-xs-12 no-padding">
		<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $info->getAttributeLabel('entry_type') ?></div>
		<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $model->entry_type ==1 ? 'Regular':'Transfer'; ?></div>
	  </div>
	</div>
</div>
<div  class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
	<div class="col-md-12 col-xs-12 col-sm-12">
	  <div class="col-lg-12 col-sm-12 col-xs-12 no-padding">
		<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $info->getAttributeLabel('nationality') ?></div>
		<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= !is_null($info->nationalityRel) ? $info->nationalityRel->name : Yii::t("stu", "Not Set") ?></div>
	  </div>
	</div>
</div>
<div  class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
	<div class="col-md-12 col-xs-12 col-sm-12">
	  <div class="col-lg-12 col-sm-12 col-xs-12 no-padding">
		<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $info->getAttributeLabel('place_of_birth_address_relId') ?></div>
		<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= !is_null($info->placeOfBirthAddressRel)?$info->placeOfBirthAddressRel->address_town .'('.$info->placeOfBirthAddressRel->addressCountryRel->name.')':'NOT SET' ?></div>
	  </div>
	</div>
</div>
<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
	 <div class="col-md-12 col-sm-12 col-xs-12 no-padding">
		<div class="col-md-6 col-sm-6 col-xs-6 edusec-profile-label edusecArLangCss" style='text-align:left; font-weight:bold;'><?= $info->getAttributeLabel('place_of_residence_address_relId') ?></div>
		<div class="col-md-6 col-sm-6 col-xs-6 edusec-profile-text"><?= !is_null($info->placeOfRecidenceAddressRel)?$info->placeOfRecidenceAddressRel->address_town . '('.$info->placeOfRecidenceAddressRel->addressCountryRel->name.')':'NOT SET' ?></div>
	</div>

</div> <!---Main Row Div--->
<script type="text/javascript">
	window.laodUpdateForm = (stdId) => {
		$('#modal').find('.modal-header').html('Update student profile');
      $('#modalContent').css('text-align','left');
      $('#modalContent').html('Loading data...');
      $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-md');
      $('#modal').modal('show');
      $.get('/ethnic-group/get-list',{stdId:stdId}).done(function(data){
          $('#modalContent').html(data);
      });
}
</script>