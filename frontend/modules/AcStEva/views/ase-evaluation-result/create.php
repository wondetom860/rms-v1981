<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEvaluationResult */

$this->title = Yii::t('rms', 'Academic Staff Performance Evaluation: ');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ase Evaluation Results'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-evaluation-result-create">

    <h4><?= Html::encode($this->title) ?> : <i><?= $model->controlSettingRel->candidateStaffRel->getFullName() ?></i></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
