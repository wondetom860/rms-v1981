<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtSetting */

$this->title = Yii::t('rms', 'Update Setting: {name}', [
    'name' => $model->setting_header_info,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->setting_header_info, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="et-setting-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
