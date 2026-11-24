<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\TRANSTUD\models\TransferStudent */

$this->title = Yii::t('rms', 'New Transfer Student');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Transfer Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-student-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
