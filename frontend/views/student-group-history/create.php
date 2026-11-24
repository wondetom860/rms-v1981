<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\StudentGroupHistory */

$this->title = Yii::t('decirs', 'Assign Students to group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student Group Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-group-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
