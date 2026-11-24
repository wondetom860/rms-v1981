<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Instructor */

$this->title = Yii::t('rms', 'Register Instructor information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Instructors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
