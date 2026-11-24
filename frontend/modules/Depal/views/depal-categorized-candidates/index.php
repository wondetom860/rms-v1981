<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\Depal\models\DepalCategorizedCandidatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Elligible Candidates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-categorized-candidates-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'setting_relId',
                'value' => function($model){
                    return $model->settingRel->sectionRel->academicYearRel->getAcHeader();
                },
                'filter' => ArrayHelper::map(frontend\modules\Depal\models\DepalCategorizedCandidates::find()->groupBy('setting_relId')->all(),'setting_relId',function($model){ return $model->settingRel->sectionRel->academicYearRel->getAcHeader(); })
            ],
            [
                'attribute' => 'category_relId',
                'value' => function($model){
                    return $model->categoryRel->category_name;
                },
                'filter' => ArrayHelper::map(frontend\modules\Depal\models\DepalCategory::find()->all(),'id',function($model){ return $model->category_name; })
            ],
            'criteria_name',
            [
                'attribute' => 'candidates',
                'value' => function($model){
                    return count(json_decode(trim($model->candidates,'"')));
                }
            ],
            'finalized',
            //'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
