<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Reassessment\models\ReassessorMark */

$this->title = Yii::t('rms', 'Create Reassessor Mark');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Reassessor Marks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reassessor-mark-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
