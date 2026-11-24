<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\GraduationRequirmentControl */

$this->title = Yii::t('decirs', 'Create Graduation Requirment Control');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Graduation Requirment Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduation-requirment-control-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
