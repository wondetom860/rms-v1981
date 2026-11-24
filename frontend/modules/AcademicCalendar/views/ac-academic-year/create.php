<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcAcademicYear */

$this->title = Yii::t('rms', 'New Academic Year');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Years'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-academic-year-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
