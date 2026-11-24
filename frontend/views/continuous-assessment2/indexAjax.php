<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

?>


<?php Pjax::begin(); ?>    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'pjax' => true,
        'export'=>false,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'header'=>'Reg ID',
                'attribute'=>'ca_std_relid',
                'value'=>function($model){
                    return $model->caStdRel->std_regId;
                }
            ],
            [
                'header'=>'Student Name',
                'attribute'=>'ca_std_relid',
                'value'=>function($model){
                    
                    $user2 = $model->caStdRel->stdUserRel;

                    return $user2->first_name . ' '. $user2->middle_name[0];
                }
            ],
            [
                'header'=>'Instance',
                'attribute'=>'ca_markAssignment_relId',
                'value'=>function($model){
                    return \backend\models\InstanceMarkAssignment::findOne($model->ca_markAssignment_relId)->ima_instanceName;
                }
            ],
            [
                'attribute'=>'ca_markObtained',
                'class'=>'kartik\grid\EditableColumn',
                'value'=>'ca_markObtained',
            ],
            [
                'header'=>'Course Title',
                'attribute'=>'ca_coff_relId',
                'value'=>function($model){
                    $coff = \backend\models\CourseOffering2Detail::findOne($model->ca_coff_relId);
                    return \backend\models\Course::findOne($coff->coff2det_course_relId)->course_title;
                }
            ],
            [
                'header'=>'Assessed at',
                'attribute'=>'ca_created_at',
                'value'=>function($model){
                    return date_format(date_create($model->ca_created_at),'d M, Y');
                }
            ],
            // 'ma_created_by',
            // 'ma_updated_by',
            // 'ma_updated_at',
            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
<?php Pjax::end(); ?>

<script type="text/javascript">
    window.myFun = function(){
        // alert(is_NAN)
    }
</script>