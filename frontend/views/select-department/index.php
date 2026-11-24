<?php
/** @var yii\web\View $this */

$this->title = Yii::t('rms', 'Select Department');
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= $this->title ?></h3>

<div class="container-fluid">
	<div class="row"  style="font-size: 10pt;">
		<div class="col-md-12 table-responsive" id="setting-entry-container">
			
		</div>
		<div class="col-md-12 hidden" id="form-container">
			
		</div>
		
	</div>
</div>

<?php 

$this->registerJs(
	"
	selectedDepts = [];
	
	$('#setting-entry-container').html('Loading Setting...');
	$.post('/select-department/get-setting-entry',{}).done((resp) => {
		selectedDepts = [];
		$('#setting-entry-container').html(resp);
	});
	",
	$this::POS_READY)

?>