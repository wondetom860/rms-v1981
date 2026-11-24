<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ThesisTitle */

$this->title = Yii::t('rms', 'Update Thesis Title: {name}, {title}', [
    'name' => $model->ttStdRel->getFullName(),'title' => $model->ttSrd2Rel->srd2SemregRel->getAcadYearHeader()
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Thesis Titles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ttStdRel->getFullName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="thesis-title-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
