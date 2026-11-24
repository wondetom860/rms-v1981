<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\TouchedGrades */

$this->title = Yii::t('rms', 'Update Touched Grades: {name}', [
    'name' => $model->tgSrd2Rel->srd2SemregRel->semregStdRel->stdUserRel->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Touched Grades'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="touched-grades-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
