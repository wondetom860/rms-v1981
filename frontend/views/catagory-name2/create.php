<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\CatagoryName2 */

$this->title = Yii::t('decirs', 'Create Catagory Name');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Catagory Name2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catagory-name2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
