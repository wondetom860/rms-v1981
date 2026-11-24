<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Course */

$this->title = Yii::t('decirs', 'Register new course information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
