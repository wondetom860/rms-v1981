<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\StudentType */

$this->title = Yii::t('decirs', 'Create Student Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
