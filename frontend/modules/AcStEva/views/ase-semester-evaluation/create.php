<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseSemesterEvaluation */

$this->title = Yii::t('rms', 'Create Semester Evaluation Entry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Evaluation Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-semester-evaluation-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
