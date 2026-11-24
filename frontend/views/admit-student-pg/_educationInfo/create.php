<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\EducationInformation */

$this->title = Yii::t('decirs', 'Education Information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Education Informations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-information-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->renderAjax('_form', [
        'sessionForm' => $sessionForm,
    ]) ?>

</div>
