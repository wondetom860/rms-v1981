<?php 
	use yii\helpers\Html;
	$adminUser = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));

	$modelEI = ($modelStudent->st_educ_info_relId)?$modelStudent->stdEducInfoRel:NULL;
 ?>

<div class="row">
  <div class="col-xs-12">
	<h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">	
		<i class="fa fa-files-o"></i> <?= Html::encode(Yii::t('stu', 'Education Documents')) ?>
	</h4>
  </div><!-- /.col -->
</div>

