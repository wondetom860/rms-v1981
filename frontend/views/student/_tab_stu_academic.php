<?php 
use yii\helpers\Html; 
use yii\widgets\DetailView;
$adminUser = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
?>


<div class="row">
  <div class="col-xs-12 col-md-12 col-lg-12">
	<h2 class="page-header">	
	<i class="fa fa-info-circle"></i> <?= Html::encode(Yii::t('stu', 'Academic Details')) ?>
	<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right'?>">
	<?= Html::a('<i class="fa fa-pencil-square-o"></i>'. Yii::t('stu', 'Edit'), ['update', 'id' => $modelStudent->id, 'tab' => 'academic'], ['class' => 'btn-sm btn btn-primary text-warning', 'id' => 'update-data']) ?>
		
	</div>
	</h2>
  </div><!-- /.col -->
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= $modelStudent->getAttributeLabel('std_prog_relId') ?></div>
	<div class="col-md-8 col-xs-8"><?= $modelStudent->stdAdmissionRel->admission_name . ' ' .  $modelStudent->stdProgRel->program_name ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= $modelStudent->getAttributeLabel('std_dept_relId') ?></div>
	<div class="col-md-8 col-xs-8"><?= !is_null($modelStudent->stdDeptRel)?$modelStudent->stdDeptRel->dept_name:"NOT SET" ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= $modelStudent->getAttributeLabel('std_spec_relId') ?></div>
	<div class="col-md-8 col-xs-8"><?= !is_null($modelStudent->stdSpecRel)?$modelStudent->stdSpecRel->spec_name:'NOT SET' ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= $modelStudent->getAttributeLabel('std_clslvl_relId') ?></div>
	<div class="col-md-8 col-xs-8"><?= $modelStudent->stdClslvlRel->clslvl_num ?></div>
  </div>
</div>

<div class="row container-fluid" style="margin-top: 4px; border-bottom: 1px solid black;">
  <div class="col-xs-12 col-md-12 col-lg-12">
	<div class="col-md-4 col-xs-4"><?= $modelUser->getAttributeLabel('status') ?></div>
	<div class="col-md-8 col-xs-8"><?= ($modelUser->status == 10) ? "Active" : (($modelUser->status == 2) ? "Inactive":"Deleted") ; ?></div>
  </div>
</div>