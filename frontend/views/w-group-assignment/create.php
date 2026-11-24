<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\WGroupAssignment */

$this->title = Yii::t('decirs', 'Assign students to a group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Group Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wgroup-assignment-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
        'modelg'=>$modelg,
    ]) ?>

</div>
