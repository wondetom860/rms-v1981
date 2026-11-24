<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcSpecialMention */

$this->title = Yii::t('rms', 'Update Special Mention: {name}', [
    'name' => $model->gsm_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Special Mentions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->gsm_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="gc-special-mention-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
