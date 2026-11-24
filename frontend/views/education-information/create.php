<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\EducationInformation */

$this->title = Yii::t('decirs', 'Create Education Information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Education Informations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-information-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
