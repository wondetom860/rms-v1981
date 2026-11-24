<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration3 */

$this->title = Yii::t('rms', 'Create Semester Registration3');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration3s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration3-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
