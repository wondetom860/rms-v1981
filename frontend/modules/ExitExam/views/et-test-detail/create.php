<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtTestDetail */

$this->title = Yii::t('rms', 'Create Et Test Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Et Test Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="et-test-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
