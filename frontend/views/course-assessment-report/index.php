<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('rms', 'Course Assessment Report');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-assessment-report-index container-fluid">
	<h3><?= Html::encode($this->title) ?></h3>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
				<h5>Filter Course Offerings</h5>
				<div class="row">
					<div class="col-md-6">
						<label>Academi Year</label>
						<input class="form-control form-xs" type="" name="">
					</div>
					<div class="col-md-6">
						<label>Semester</label>
						<input class="form-control form-xs" type="" name="">
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<small>waiting for your selection</small>
			</div>
		</div>
	</div>
</div>
