<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\CourseCatalog */

$this->title = Yii::t('decirs', 'Set Course Catalog');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Course Catalogs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="course-catalog-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
