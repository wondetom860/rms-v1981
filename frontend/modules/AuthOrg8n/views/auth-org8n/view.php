<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model common\models\AuthOrg8n */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Authenticated Orgaization Information'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-org8n-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a(Yii::t('decris', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('decris', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('decris', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4" style="background-color: transparent;">
            <img width="300px" style="background-color: transparent;" height="auto" style="padding: 10px; border-radius: 20px;" src="/uploads/logos/<?= $model->logo ?>">
        </div>
        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name:ntext',
                    'abrvn',
                    'website',
                    'email:email',
                    'phone1',
                    'phone2',
                    'active_from',
                    'active_to',
                    'lic_entry',
                    'updated_by',
                    'updated_at',
                    'created_at',
                    'created_by',
                    'data:ntext',
                ],
            ]) ?>
        </div>
    </div>
</div>
