<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\GraduationEligiblityMaster */

$this->title = Yii::t('rms', 'Create Graduation Eligiblity Master');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduation Eligiblity Masters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduation-eligiblity-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
