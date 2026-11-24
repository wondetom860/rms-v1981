<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\ContAssessMarkAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Instance Mark Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-mark-assignment-index">

    <h4><?= Html::encode($this->title) ?></h4>
<?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <div class="cont-assess-mark-assignment-form container-fluid" style="background-color: #393;">

        <?php $form = ActiveForm::begin(['action'=>'/cont-assess-mark-assignment2/create']); ?>

        <div class="row">
             <div class="col-md-3">
                <?= $form->field($model, 'cat_coff_relId')->dropdownList(
                        ArrayHelper::map($model->getInstCourse2(),'id','course_title')
                        ,[
                            'prompt'=>'Select Course...',
                            'onchange'=>'GetCatagoryList($(this).val(),null)',
                        ]
                    ) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'ma_catagory_relId')->dropdownList(
                        ArrayHelper::map(\backend\models\ContAssessCatagoryName::find()->where(['<>','cont_cat_name','Final Exam'])->all(),'id','cont_cat_name')
                        ,[
                            'prompt'=>'Select Catagory Name...',
                            'onchange'=>'getInstance($(this).val(),null)',
                        ]
                    ) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'ma_instance')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'ma_possiblePoint')->textInput() ?>
            </div>
            <div class="col-md-1"><br>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

    </div>

    <?php Pjax::begin(); ?>    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'cat_coff_relId',
                'value'=>function($model){
                    $courseId = \backend\models\CourseOffering2Detail::findOne($model->cat_coff_relId)->coff2det_course_relId;
                    return \backend\models\Course::findOne($courseId)->course_title;
                }
            ],
            [
                'attribute'=>'ma_catagory_relId',
                'value'=>function($model){
                    $CatagoryId = \backend\models\ContAssessCatagory2::findOne($model->ma_catagory_relId)->cat_name_relId;
                    return \backend\models\ContAssessCatagoryName::findOne($CatagoryId)->cont_cat_name;
                }
            ],
            'ma_instance',
            'ma_possiblePoint',
            [
                'attribute'=>'ma_created_by',
                'value'=>'maCreatedBy.first_name',
            ],
            [
                'attribute'=>'ma_created_at',
                'value'=> function($model){
                    return date_format(date_create($model->ma_created_at),'D, M d, Y');
                } ,
            ],
             
            // 'ma_updated_by',
            // 'ma_updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
<?php Pjax::end(); ?></div>

<?php

$this->registerJs(
    "

    window.generatePJAXSearcQuery = function(coffdetID2){
        coffdetID = coffdetID2;
        $.post('/cont-assess-mark-assignment2/index?ContAssessMarkAssignment2Search%5Bcat_coff_relId%5D='+coffdetID+'&ContAssessMarkAssignment2Search%5Bma_catagory_relId%5D=&ContAssessMarkAssignment2Search%5Bma_instance%5D=&ContAssessMarkAssignment2Search%5Bma_possiblePoint%5D=&ContAssessMarkAssignment2Search%5Bma_created_at%5D=',{}).done(function(data){
            $('#w1').html(data);
        });
    }

    window.generatePJAXSearcQuery2 = function(catagoryId){
        $.post('/cont-assess-mark-assignment2/index?ContAssessMarkAssignment2Search%5Bcat_coff_relId%5D='+coffdetID+'&ContAssessMarkAssignment2Search%5Bma_catagory_relId%5D='+catagoryId+'&ContAssessMarkAssignment2Search%5Bma_instance%5D=&ContAssessMarkAssignment2Search%5Bma_possiblePoint%5D=&ContAssessMarkAssignment2Search%5Bma_created_at%5D=',{}).done(function(data){
            $('#w1').html(data);
        });
    }
    // http://rmsfront.local



    var coffdetID = '';
    var courseId = '';
    var studentGradesString = null;
    window.getInstance = function(dd){
        // generatePJAXSearcQuery2(dd);
        // $('#markSubmissionFormContainer').hide();
        instanceName = $('#contassessmarkassignment2-ma_catagory_relid :selected').data('slice');
        maxPoint = $('#contassessmarkassignment2-ma_catagory_relid :selected').data('max');
        // remPoint = $('#contassessmarkassignment2-ma_catagory_relid :selected').data('rem');
        // $('#maxPointContainer').val(remPoint).parent().show();
        $('#contassessmarkassignment2-ma_possiblepoint').prop({'max':maxPoint});
        $('#contassessmarkassignment2-ma_possiblepoint').val(maxPoint).focus();
        $('#contassessmarkassignment2-ma_instance').val(instanceName);

    }
    window.GetCatagoryList = function(coffdetID){
        generatePJAXSearcQuery(coffdetID);
        $.post('/cont-assess-mark-assignment2/get-catagory',
            {
                courseId:coffdetID
            }).done(function(data){
                $('#contassessmarkassignment2-ma_catagory_relid').html(data);
                getInstance();
            });
    }

    window.loadMarkSheetForm = function(coffdetID,courseId){
        $.post('/cont-assess-mark-assignment2/get-assessment-submission-form',
        {
            instId:coffdetID,
            courseId:courseId
        }).done(function(data){
            $('#submitmarkContainer').html(data).parent().show();
        });
    }
    ",
    $this::POS_READY);
?>
