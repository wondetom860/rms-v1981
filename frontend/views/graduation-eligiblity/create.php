<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\GraduationEligiblity */

$this->title = Yii::t('rms', 'Create Graduation Eligiblity');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduation Eligiblities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduation-eligiblity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
