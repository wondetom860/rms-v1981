<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaDescription */

$this->title = Yii::t('rms', 'Update Criteria Description: {name}', [
    'name' => $model->code,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Criteria Descriptions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => substr($model->description, 0,9).'...', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ase-criteria-description-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
