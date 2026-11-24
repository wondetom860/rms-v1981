<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\InstanceMarkAssignment */

$this->title = Yii::t('decirs', 'Create Instance Mark Assignment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Instance Mark Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instance-mark-assignment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
