<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAcademicWorkDay */

$this->title = Yii::t('rms', 'Add Academic Work Day');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Work Days'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-academic-work-day-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
