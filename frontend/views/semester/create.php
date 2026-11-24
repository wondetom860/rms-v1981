<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Semester */

$this->title = Yii::t('decirs', 'Create Semester');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semesters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
