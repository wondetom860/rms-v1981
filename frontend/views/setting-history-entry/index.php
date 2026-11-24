<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
// use backend\models\User;
// use backend\models\StudentType;
// use backend\models\Student;
// use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Grade Scale Setting History Entry');
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= $this->title ?></h3>

<div class="setting-history-entrt-index container-fluid table-responsive">
	<?= $this->render('__report/index-report') ?>
</div>

