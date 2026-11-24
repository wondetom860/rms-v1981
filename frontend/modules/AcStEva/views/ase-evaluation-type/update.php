<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEvaluationType */

$this->title = Yii::t('rms', 'Update Evaluation Type: {name}', [
    'name' => $model->type_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ase Evaluation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->type_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ase-evaluation-type-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
