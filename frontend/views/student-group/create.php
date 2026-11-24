<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\StudentGroup */

$this->title = Yii::t('decirs', 'Create Student Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-group-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1>
 -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
