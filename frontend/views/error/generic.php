<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\GradeScaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Error');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grade-scale-index">
	<div class="row">>
	    <div class="col-md-6">
	        <div class="alert alert-danger" role="alert">
	  			<h2>Something went wrong. Please check your inputs</h2>
			</div>
		</div>
	</div>
</div>