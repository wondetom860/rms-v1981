<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaBundle */

$this->title = Yii::t('rms', 'Create Type-Criteria Bundle');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Criteria Bundles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-criteria-bundle-create">

    <h3><?= Html::encode($this->title).": {$model->evalTypeRel->type_name}" ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
