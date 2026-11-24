<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2 */

$this->title = Yii::t('rms', 'Create Semester Registration2');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
