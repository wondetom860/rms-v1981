<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\PgPostSecondarySchool */

$this->title = Yii::t('decirs', 'Create Pg Post Secondary School');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Pg Post Secondary Schools'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pg-post-secondary-school-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
