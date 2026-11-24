<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEvaluationControlSetting */

$this->title = Yii::t('rms', 'Create Ase Evaluation Control Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ase Evaluation Control Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-evaluation-control-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
