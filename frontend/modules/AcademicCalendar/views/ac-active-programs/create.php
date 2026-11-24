<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcActivePrograms */

$this->title = Yii::t('rms', 'New Active Program');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Active Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-active-programs-create">

    <h3><?= Html::encode($this->title)." >> ".$model->academicYearRel->getAcHeader() ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'records' => $records,
        'ayId' => $model->academic_year_relId,
    ]) ?>

</div>
