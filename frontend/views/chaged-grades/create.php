<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\TouchedGrades */

$this->title = Yii::t('rms', 'Request student grade change');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Changed Grades'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touched-grades-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
