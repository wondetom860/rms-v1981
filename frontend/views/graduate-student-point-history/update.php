<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentPointHistory */

$this->title = Yii::t('rms', 'Update Graduate Student Point History: {name}', [
    'name' => $model->gphGstdRel->gstdUserRel->first_name. ' ('. $model->gphGstdRel->gstdProgRel->program_name.')' ,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduate Student Point Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->gphGstdRel->gstdUserRel->first_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="graduate-student-point-history-update">

    <h3 style="border-bottom: 1px solid black;"><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form2', [
        'model' => $model,
    ]) ?>

</div>
