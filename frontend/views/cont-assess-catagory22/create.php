<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessCatagory */

$this->title = Yii::t('decirs', 'Create Cont Assess Catagory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Cont Assess Catagories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-catagory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
