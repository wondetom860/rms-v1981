<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ContinuousAssessment2 */

$this->title = Yii::t('decirs', 'Create Continuous Assessment2');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Continuous Assessment2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="continuous-assessment2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
