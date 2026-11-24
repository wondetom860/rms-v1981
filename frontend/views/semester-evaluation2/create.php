<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluation2 */

$this->title = Yii::t('decirs', 'Create Semester Evaluation2');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
