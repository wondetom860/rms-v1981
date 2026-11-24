<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\PgSecondarySchoolsAttended */

$this->title = Yii::t('decirs', 'Create Pg Secondary Schools Attended');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Pg Secondary Schools Attendeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pg-secondary-schools-attended-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
