<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalChoice */

$this->title = Yii::t('rms', 'Update Depal Choice: {name}', [
    'name' => $model->stdRel->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Choices'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="depal-choice-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render(Yii::$app->user->can('Student') ? '_form-new' : '_form', [
        'model' => $model,
    ]) ?>

</div>
