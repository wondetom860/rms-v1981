<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Department */

$this->title = Yii::t('decirs', 'Add Department');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Departments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
