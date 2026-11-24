<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\GradeScale */

$this->title = Yii::t('decirs', 'Create Grade Scale');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Grade Scales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grade-scale-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
