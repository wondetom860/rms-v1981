<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\User */

$this->title = Yii::t('decirs', 'Personal Information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
	
    <h2><?= Html::encode($this->title) ?></h2> 

    <?= $this->renderAjax('_form', [
        'sessionForm' => $sessionForm,
    ]) ?>

</div>
