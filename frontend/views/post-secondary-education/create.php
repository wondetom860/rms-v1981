<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducation */

$this->title = Yii::t('decirs', 'Create Post Secondary Education');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Post Secondary Educations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-secondary-education-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
