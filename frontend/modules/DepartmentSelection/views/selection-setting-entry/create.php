<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\SelectionSettingEntry */

$this->title = Yii::t('decris', 'New Selection Setting Entry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Setting Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="selection-setting-entry-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
