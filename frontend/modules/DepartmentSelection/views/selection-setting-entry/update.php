<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\SelectionSettingEntry */

$this->title = Yii::t('decris', 'Update Selection Setting Entry: {name}', [
    'name' => NULL,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Selection Setting Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="selection-setting-entry-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
