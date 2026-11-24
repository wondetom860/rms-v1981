<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSemesterEvaluation */

$this->title = Yii::t('decris', 'Create Wsemester Evaluation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wsemester Evaluations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wsemester-evaluation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
