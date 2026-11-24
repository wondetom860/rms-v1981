<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SentPublishdGrades */

$this->title = Yii::t('rms', 'Create Sent Publishd Grades');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Sent Publishd Grades'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sent-publishd-grades-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
