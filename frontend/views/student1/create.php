<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Student1 */

$this->title = Yii::t('decirs', 'Create Student1');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student1s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student1-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
