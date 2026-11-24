<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\AcademicRank */

$this->title = Yii::t('decirs', 'Create Academic Rank');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Ranks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-rank-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
