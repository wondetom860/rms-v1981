<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;


/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducation */

$this->title = Yii::t('decirs', 'Post Secondary Education');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Post Secondary Educations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-secondary-education-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->renderAjax('_form', [
    	'sessionForm'=>$sessionForm
    ]) ?>

</div>
