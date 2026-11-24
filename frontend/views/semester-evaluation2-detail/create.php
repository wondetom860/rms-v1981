<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluation2Detail */

$this->title = Yii::t('decirs', 'Create Semester Evaluation2 Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation2 Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation2-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
