<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\StdStatusCalculationControlDefaultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Readmission Calculation Controls');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Readmission Calculation Controls-index">

    <h3><?= Html::encode($this->title) ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); 
        ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create Readmission Calculation Control'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>
    <?php Pjax::begin(); ?> 
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    // ['class' => 'yii\grid\SerialColumn'],

                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return [
                                'value' => $model->sssccd_min_SGPA,
                                'onchange' => '
                                    if($(this)[0].checked){
                                        $(this).parent().parent().addClass("success");
                                    }else{
                                        $(this).parent().parent().removeClass("success");
                                    }
                                    ',
                            ];
                        }
                    ],

                    [
                        'attribute' => 'sssccdAdmRel.admission_name',
                        'value' => 'sssccdAdmRel.admission_name',
                    ],
                    [
                        'attribute' => 'sssccdProgRel.program_name',
                        'value' => 'sssccdProgRel.program_name',
                    ],
                    [
                        'attribute' => 'sssccdClslvlRel.clslvl_num',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->sssccdClslvlRel->getClslvlDisp();
                            // $clslvlNum = \backend\models\ClassLevel::findOne(['id'=>$model->sssccd_clslvl_relId])->clslvl_num;
                            // return $clslvlNum == 1?'1<sup>st</sup>':($clslvlNum == 2?'2<sup>nd</sup>':($clslvlNum == 3?'<sup>rd</sup>':$clslvlNum.'<sup>th</sup>'));
                        },
                    ],
                    [
                        'attribute' => 'sssccdSemRel.semester_name',
                        'value' => 'sssccdSemRel.semester_name',
                    ],

                    [
                        'attribute' => 'sssccd_min_SGPA',
                        'value' => function ($model) {
                            return $model->sssccd_min_SGPA ? sprintf('%.2f', $model->sssccd_min_SGPA) : ' - ';
                        }
                    ],
                    [
                        'attribute' => 'sssccd_max_SGPA',
                        'value' => function ($model) {
                            return $model->sssccd_max_SGPA ? sprintf('%.2f', $model->sssccd_max_SGPA) : ' - ';
                        }
                    ],
                    [
                        'attribute' => 'sssccd_min_CGPA',
                        'value' => function ($model) {
                            return $model->sssccd_min_CGPA ? sprintf('%.2f', $model->sssccd_min_CGPA) : ' - ';
                        }
                    ],
                    [
                        'attribute' => 'sssccd_max_CGPA',
                        'value' => function ($model) {
                            return $model->sssccd_max_CGPA ? sprintf('%.2f', $model->sssccd_max_CGPA) : ' - ';
                        }
                    ],
                    //'sssccd_remark:ntext',
                    [
                        'attribute' => 'sssccdCreatedBy.username',
                        'value' => 'sssccdCreatedBy.username',
                    ],

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
    <?php Pjax::end(); ?>
</div>


<?php
$this->registerJs(
    "
    $('selection').change(function(){
        console.log('neemsk');
        $(this).parent().parent().highLight();
    })

    ",
    $this::POS_READY
);

?>