<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalander */

$this->title = Yii::t('decirs', 'New Academic Calander');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Calanders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-calander-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
