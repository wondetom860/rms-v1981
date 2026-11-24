<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluation */

$this->title = Yii::t('decirs', 'Create Semester Evaluation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
