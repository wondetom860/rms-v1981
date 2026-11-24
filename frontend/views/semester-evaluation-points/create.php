<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationPoints */

$this->title = Yii::t('decirs', 'Create Semester Evaluation Points');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation Points'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-points-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
