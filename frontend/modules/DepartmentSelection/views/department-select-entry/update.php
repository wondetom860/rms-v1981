<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\DepartmentSelectEntry */

$this->title = Yii::t('decris', 'Update Department Select Entry: {name}', [
    'name' => $model->getTitle(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Department Select Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="department-select-entry-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
