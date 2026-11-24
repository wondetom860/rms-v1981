<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationDetail */

$this->title = Yii::t('decirs', 'Create Semester Evaluation Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
