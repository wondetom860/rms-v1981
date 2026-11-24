<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtElligiblityTest */

$this->title = Yii::t('rms', 'Register for Elligiblity Test');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Test Trials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="et-elligiblity-test-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
