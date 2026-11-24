<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Post Secondary Educations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-secondary-education-view">

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
            'pse_ei_relId',
            'pse_institute_name:ntext',
            'pse_nationality_relId',
            'pse_from_year',
            'pse_to_year',
            'pse_credit_earned',
            'pse_CGPA_obtained',
            'pse_certificate_earned:ntext',
            'pse_created_by',
            'pse_updated_by',
            'pse_created_at',
            'pse_updated_at',
        ],
    ]) ?>

</div>
