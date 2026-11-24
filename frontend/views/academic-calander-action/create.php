<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalanderAction */

$this->title = Yii::t('decirs', 'Create Academic Calander Action');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Calander Actions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-calander-action-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
