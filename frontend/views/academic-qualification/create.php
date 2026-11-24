<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\AcademicQualification */

$this->title = Yii::t('decirs', 'Create Academic Qualification');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Qualifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-qualification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
