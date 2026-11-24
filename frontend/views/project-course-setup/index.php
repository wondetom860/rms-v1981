<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

use \common\controllers\CommonControllerFunctions as CCF;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ProjectCourseSetupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Project Group Setups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-course-setup-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?= Html::a(Yii::t('decirs', 'Create Project Course Setup'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <div class="conatiner-fluid">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="text-align: center; text-decoration: underline; text-shadow: 2px;">Create new Project group setup for this semester</h4>
            </div>
            <div class="panel-body">
                <div class="conatiner-fluid">
                    <div class="row">
                        <?php $form = ActiveForm::begin(['action'=>'/project-course-setup/create']); ?>

                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_acadYear_relId')->textInput([
                                    'value'=>Yii::$app->params['C']['Y'],
                                    'readonly'=>true,
                                    'title'=>'You are offering courses for this Acad. Year'
                                ]) ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_sem_relId')->textInput([
                                'value'=>\backend\models\Semester::findOne(['id'=>CCF::currentTime()['S']])->semester_name,
                                'readonly'=>true,
                                'title'=>'You are offering courses for this semester.'
                            ]) ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_gset_relId')->dropdownList(
                                ArrayHelper::map($model->getGsetArray(1),'id','gname'),
                                [
                                    'prompt'=>'Select Group',
                                    'onChange'=>'
                                        $.post("/project-course-setup/free-project-groups",{gsetId:$(this).val() }).done(function(data){
                                            $("#projectcoursesetup-pcs_pgname_relid").html(data);
                                        });
                                    ',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_course_relId')->dropdownList(
                                ArrayHelper::map($model->getDeptProjectCourse(1),'id','course_title'),
                                [
                                    'prompt'=>'Select course',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_pgName_relId')->dropdownList(
                                [],
                                [
                                    'prompt'=>'Select project Group',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_advisor')->textInput() ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_group_leader')->textInput() ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'pcs_title')->textarea(['rows' => 3]) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="conatiner-fluid table-responsive" style="background-color: button; border: 1px solid black;">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'pcs_acadYear_relId',
                'value'=>'pcsAcadYearRel.sch_year_desc',
            ],
            [
                'attribute'=>'pcs_sem_relId',
                'value'=>'pcsSemRel.semester_name',
            ],

            [
                'attribute'=>'pcs_gset_relId',
                'value'=>function($model){
                    if (is_null($model->pcs_gset_relId)) {
                       return;
                    }else{
                        $gid = \backend\models\WGroupSetup::findOne(['id'=>$model->pcs_gset_relId]);
                        return \backend\models\WGroup::findOne(['id'=>$gid->gs_group_relId])->group_name;
                    }
                }
            ],
            [
                'attribute'=>'pcs_course_relId',
                'value'=>'pcsCourseRel.course_title',
            ],
            [
                'attribute'=>'pcs_pgName_relId',
                'value'=>'pcsPgNameRel.pg_name',
            ],
            'pcs_advisor',
            'pcs_group_leader',
            'pcs_title:ntext',
            // 'pcs_approved',
            // 'pcs_approved_at',
            // 'pcs_approved_by',
            // 'pcs_created_at',
            // 'pcs_created_by',
            // 'pcs_updated_at',
            // 'pcs_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?>
    </div>
</div>
