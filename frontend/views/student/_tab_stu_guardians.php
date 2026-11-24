<?php

use yii\helpers\Html;

$adminUser = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));

// $emConta = $modelStudent->std_emergency_contact_relId
$emConta = $modelStudent->stdEmergencyContactRel;

// if (is_null($emConta)) {
// 	return "Emergency Contact NOT set.";
// }
?>

<div class="row">
	<div class="col-xs-12 col-md-12 col-lg-12">
		<h2 class="page-header">
			<i class="fa fa-info-circle"></i> <?= Html::encode(Yii::t('stu', 'Emergency Contacts Details')) ?>
			<?php
			if (empty($emConta)) { ?>
				<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right' ?>">
					<?= Html::a('<i class="fa fa-user-plus"></i> ' . Yii::t('stu', 'Add Guardian'), ['#'], ['class' => 'btn-sm btn btn-primary text-warning', 'id' => 'update-guard-data', 'onclick' => "addGuardian({$modelStudent->id});return false"]) ?>
				</div>
			<?php } ?>
		</h2>
	</div><!-- /.col -->
</div>

<?php
if (!empty($emConta)) {
	$i = 1;
	foreach ($emConta as $sd) { ?>

		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<h2 class="page-header edusec-border-bottom-warning">
					<?= $i . "-" . $sd['ec_first_name'] ?>
					<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right' ?>">
						<?= Html::a('<i class="fa fa-pencil-square-o"></i> ' . Yii::t('stu', 'Edit'), ['#'], ['onclick' => "updateEC({$sd['id']});return false;"]) ?>

						<?= Html::a('<i class="fa fa-trash-o"></i> ' . Yii::t('stu', 'Delete'), ['/emergency-contact/delete', 'id' => $sd['id']], [
							'class' => 'btn btn-danger btn-sm',
							'data' => [
								'confirm' => Yii::t('stu', 'Are you sure you want to delete this item?'),
								'method' => 'post',
							],
						]) ?>


					</div>
				</h2>
			</div><!-- /.col -->
		</div>

		<?php
		// **** Update Status of is_emp_contact on student Guardian tab ****
		$url = yii\helpers\Url::toRoute(["stu-master/emg-change-status"]);
		$JSClick = <<<EOF
function(event, state) {
	var st_id = this.value;
	var sid = $( this ).attr( "sid" );
	var guard_id = $( this ).attr( "guard_id" );
	$.ajax({
		type: "POST",
		url: "{$url}",
		data: { sid: sid, guard_id: guard_id },
		success: function(result){
			//window.location = 'index';
		}
	});
}
EOF;
		?>

		<div class="row">
			<div class="col-lg-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid black; margin-top: 5px;">
				<div class="col-lg-3 col-sm-3 col-xs-6 edusec-profile-label edusecArLangCss"><?= $sd->getAttributeLabel('ec_first_name') ?></div>
				<div class="col-lg-9 col-sm-9 col-xs-6 edusec-profile-text"><?= $sd['ec_first_name'] . ' ' . $sd['ec_last_name'] ?></div>
			</div>

			<div class="col-md-12 col-xs-12 col-sm-12" style="border-bottom: 1px solid black; margin-top: 5px;">
				<div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
					<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss"><?= $sd->getAttributeLabel('ec_relationShip') ?></div>
					<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $sd['ec_relationShip'] ?></div>
				</div>
			</div>

			<div class="col-md-12 col-xs-12 col-sm-12" style="border-bottom: 1px solid black; margin-top: 5px;">
				<div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
					<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss"><?= $sd->getAttributeLabel('ec_phone_home') ?></div>
					<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $sd['ec_phone_home'] ?></div>
				</div>
				<div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
					<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss"><?= $sd->getAttributeLabel('ec_phone_mobile') ?></div>
					<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $sd['ec_phone_mobile'] ?></div>
				</div>
			</div>

			<div class="col-md-12 col-xs-12 col-sm-12" style="border-bottom: 1px solid black; margin-top: 5px;">
				<div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
					<div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss"><?= $sd->getAttributeLabel('ec_email') ?></div>
					<div class="col-lg-6 col-xs-6 edusec-profile-text"><?= $sd['ec_email'] ?></div>
				</div>
			</div>

			<div class="col-md-12 col-xs-12 col-sm-12" style="border-bottom: 1px solid black; margin-top: 5px;">
				<div class="col-md-3 col-xs-6 col-sm-3 edusec-profile-label edusecArLangCss"><?= $sd->getAttributeLabel('ec_address_relId') ?></div>
				<div class="col-md-9 col-xs-6 col-sm-9 edusec-profile-text"><?= $sd->ecAddressRel->addressCountryRel->name ?></div>
			</div>

		</div>
	<?php
		$i++;
	}
} else {
	?>
	<table width="100%" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-hover table-responsive">
		<tr>
			<th class="table-cell-title text-center" colspan=4><?= Yii::t("stu", "No Data Available") ?></th>
		</tr>
	</table>
<?php
}
?>

<script>
	const addGuardian = (id) => {
		// load update form of Emergence contact
		$('#modal').find('.modal-header').html('Add Emergncey Contact Info');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading data...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/emergency-contact/create', {
			id: id,
			ar_request: 1,
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
	const updateEC = (id) => {
		// load update form of Emergence contact
		$('#modal').find('.modal-header').html('Update Emergncey Contact Info');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading data...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/emergency-contact/update', {
			id: id,
			ar_request: 1,
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
</script>