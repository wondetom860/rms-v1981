<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\User */

$this->title = Yii::t('decirs', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->renderAjax('_form', [
        'model' => $model,
    ]) ?>

</div>
