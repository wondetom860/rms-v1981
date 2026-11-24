<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalChoice */

$this->title = Yii::t('rms', 'Choose departments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Choices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-choice-create">

    <h4 style="text-align: center;"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form-new', [
        'model' => $model,
    ]) ?>

</div>
