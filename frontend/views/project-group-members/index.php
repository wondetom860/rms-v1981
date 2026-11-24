<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ProjectGroupMembersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Project Group Members');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-group-members-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?= Html::a(Yii::t('decirs', 'Create Project Group Members'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

       <div class="conatiner-fluid">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="text-align: center; text-decoration: underline; text-shadow: 2px;">Create new Project group setup for this semester</h4>
            </div>
            <div class="panel-body">
                <div class="conatiner-fluid">
                    <div class="row">
                        <?php $form = ActiveForm::begin(['action'=>'/project-group-members/create']); ?>


                        <div class="col-md-3">
                            <?= $form->field($model, 'pgm_pgSetup_relId')->dropdownList(
                                ArrayHelper::map($model->getDeptProjectGroups(),'id','pg_name'),
                                [
                                    'prompt'=>'Select Group',
                                    'onchange'=>'

                                        $.post("/project-group-members/student-list",{pgsetId:$(this).val()}).done(function(data){
                                                $("#studentList").html(data);
                                        });

                                    ',
                                ]
                            ) ?>
                        </div>

                         <div class="col-md-3">
                            <div class="panel panel-secondary">
                                <div class="panel-heading">
                                     Select students for the Group
                                </div>
                                <div class="panel-body">
                                    <div class="conatiner-fluid" id="studentList">
                                        Waiting for your selection...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>

                        </div>  
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'pgm_pgSetup_relId',
            'pgm_std_relId',
            'pgm_enroll_type',
            'pgm_grade_relId',
            // 'pgm_gradeApproved',
            // 'pgm_gradeApproved_by',
            // 'pgm_gradeApproved_at',
            'pgm_advisor_comment:ntext',
            // 'pgm_created_at',
            // 'pgm_craeted_by',
            // 'pgm_updated_by',
            // 'pgm_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
