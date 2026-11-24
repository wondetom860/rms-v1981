<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessCatagory2 */

$this->title = Yii::t('decirs', 'Create Cont Assess Catagory2');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Cont Assess Catagory2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-catagory2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
