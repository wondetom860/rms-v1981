<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalSetting */

$this->title = Yii::t('rms', 'Create New Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-setting-create">

    <h3><?= Html::encode($this->title) ?> >>> Determines department selection</h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
