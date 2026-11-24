<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Reassessment\models\ReassessorMark */

$this->title = Yii::t('rms', 'Update Reassessor Mark: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Reassessor Marks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="reassessor-mark-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
