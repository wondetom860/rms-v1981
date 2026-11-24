<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\DepartmentHead */

$this->title = Yii::t('rms', 'Assign New Department Head');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Heads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-head-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
