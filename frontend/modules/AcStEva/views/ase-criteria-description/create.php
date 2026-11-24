<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaDescription */

$this->title = Yii::t('rms', 'Register Criteria Description');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Criteria Descriptions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-criteria-description-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
