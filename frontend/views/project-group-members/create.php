<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ProjectGroupMembers */

$this->title = Yii::t('decirs', 'Create Project Group Members');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Project Group Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-group-members-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
