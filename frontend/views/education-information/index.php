<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\EducationInformationSeasrch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Education Informations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-information-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Education Information'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ei_G10_doc_scan:ntext',
            'ei_G12_doc_scan:ntext',
            'ei_enteranceMarkPoint',
            'ei_created_at',
            // 'ei_updated_at',
            // 'ei_created_by',
            // 'ei_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
