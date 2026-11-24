<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\FacultyStaff */

$this->title = Yii::t('rms', 'Create Faculty Staff Control');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Faculty Staff'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faculty-staff-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
