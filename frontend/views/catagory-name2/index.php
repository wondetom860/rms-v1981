<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CatagoryName2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Assessment Catagory Name');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catagory-name2-index">

    <h3><?= Html::encode($this->title) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('decirs', 'Add New Assessment Catagory Name'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>
    </h3>
    <div class="container-fluid table-responsive" style="border: 1px solid black; padding: 5px;">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'catagory_name',
                'remark:ntext',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
