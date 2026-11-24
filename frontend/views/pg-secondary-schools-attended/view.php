<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\PgSecondarySchoolsAttended */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Pg Secondary Schools Attendeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pg-secondary-schools-attended-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('decirs', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('decirs', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('decirs', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'pssa_pei_relId',
            'pgssa_name_of_school:ntext',
            'pgssa_location:ntext',
            'pgssa_years_from',
            'pgssa_years_to',
        ],
    ]) ?>

</div>
