<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessMarkAssignment */

$this->title = Yii::t('decirs', 'Create Cont Assess Mark Assignment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Cont Assess Mark Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-mark-assignment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
