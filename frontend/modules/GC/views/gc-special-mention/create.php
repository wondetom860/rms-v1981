<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcSpecialMention */

$this->title = Yii::t('rms', 'Create Special Mention');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Gc Special Mentions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gc-special-mention-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
