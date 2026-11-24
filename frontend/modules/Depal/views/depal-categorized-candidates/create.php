<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalCategorizedCandidates */

$this->title = Yii::t('rms', 'Create Depal Categorized Candidates');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal Categorized Candidates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-categorized-candidates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
