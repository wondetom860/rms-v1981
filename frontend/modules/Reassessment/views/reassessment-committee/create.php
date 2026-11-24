<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Reassessment\models\ReassessmentCommittee */

$this->title = Yii::t('rms', 'Create Reassessment Committee');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Reassessment Committees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reassessment-committee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
