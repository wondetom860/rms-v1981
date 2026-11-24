<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSemesterEvaluationDetail */

$this->title = Yii::t('decris', 'Create Wsemester Evaluation Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wsemester Evaluation Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wsemester-evaluation-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
