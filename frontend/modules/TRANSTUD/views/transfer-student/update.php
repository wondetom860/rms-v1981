<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\TRANSTUD\models\TransferStudent */

$this->title = Yii::t('rms', 'Update Transfer Student: {name}', [
    'name' => $model->stdRel->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Transfer Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->stdRel->std_regId, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="transfer-student-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
