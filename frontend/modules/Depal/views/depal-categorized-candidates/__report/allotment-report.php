<?php 

if (isset($model) && !empty($model)) {
?>
<div class="container-fluid">
	<h5 style="text-align: center;">
		Department Allocation Report: (<?= $model->finalized ? "<i><b>Final</b></i>":"<i><b>Tentative</b></i>" ?>)
		<br>Academic Year: <i><u><?= $model->settingRel->sectionRel->getAcadYearHeader()." - ".$model->settingRel->sectionRel->getClassLevelHeader() ?></u></i>
		<br>Allotment Criteria Category: <i><u><?= $model->categoryRel->category_name."({$model->criteria_name})" ?></u></i>
	</h5>
</div>
<?= $this->render('grouped-candidates-report',['model' => $model]) ?>
<?php }else{
	echo " Invalid record provided";
}

?>