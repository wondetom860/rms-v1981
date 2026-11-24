<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\DepartmentSelectEntry */

$this->title = Yii::t('decris', 'Create Department Select Entry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Department Select Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-select-entry-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
