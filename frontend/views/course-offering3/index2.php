<?php 


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
// use \common\controllers\CommonControllerFunctions as CCF;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2Detail */
/* @var $form yii\widgets\ActiveForm */

$this->title = "Course Offering";
// $this->title = Yii::t('rms', 'Course Offering and Enrollment Detail');
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="course-offering-index">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-5">
				<h4>Academic Programs (Active for Academic Year) </h4>
				<div class="container-fluid">
					<?= $this->render('../modules/AcademicCalendar/views/ac-active-programs/') ?>
				</div>
			</div>
			<div class="col-md-7">
				<h4>Course Offering inforamtion</h4> 
			</div>
		</div>
	</div>
	<div class="row">
		
	</div>
</div>