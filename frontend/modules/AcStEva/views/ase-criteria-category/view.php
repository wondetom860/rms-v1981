<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaCategory */

$this->title = $model->category_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Criteria Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ase-criteria-category-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
        <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-xs',
            'data' => [
                'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'category_name',
            [
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->createdBy->username;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            [
                'attribute' => 'Evalation Criteria Desctription',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('../ase-criteria-description/__report/criteria-by-category',['model' => $model]);
                }
            ],[
                'attribute' => 'profess_list',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('__report/employee-professions',['model' => $model]);
                }
            ],
        ],
    ]) ?>

</div>

<?php

 Modal::begin([
    'header'=>'<h4>Penality registration payment form</h4>',
    'id'=>'modal',
    'size'=>'modal-lg',
 ]);
 echo "<div id='modalContent'>Loading payment form...</div>";
 Modal::end();

 ?>
