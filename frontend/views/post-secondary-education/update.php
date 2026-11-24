<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducation */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Post Secondary Education',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Post Secondary Educations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="post-secondary-education-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
