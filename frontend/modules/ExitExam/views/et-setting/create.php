<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtSetting */

$this->title = Yii::t('rms', 'Create New Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="et-setting-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
