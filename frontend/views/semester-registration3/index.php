<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistration3Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration3-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?= Html::a(Yii::t('rms', 'Create Semester Registration3'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
 -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Your current semester registration status
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <?php
                                    echo "<b>Acc. Year: </b><i><u>".Yii::$app->params['currentAcademicStatus']['year'].'</u></i>';
                                    echo "<br><b> Semester: </b><i><u>".Yii::$app->params['currentAcademicStatus']['semester'].'</u></i>';

                                    $adm = backend\models\Admission::find()->where(['id'=>$studModel[0]['std_admission_relId']])->one();
                                        echo "<br><b> Admission: </b><i><u>".$adm->admission_desc.'</u></i>';
                                    ?>
                            </div>
                            <div class="col-md-4">
                                <?php

                                $pgm = backend\models\Program::find()->where(['id'=>$studModel[0]['std_prog_relId']])->one();
                                echo "<br><b> Program: </b><i><u>".$pgm->program_desc.'</u></i>';
                                
                               
                                $dept = \backend\models\Department::find()->where(['id'=>$studModel[0]['std_dept_relId']])->one();
                                echo "<br><b> Department: </b><i><u>".$dept->dept_name.'</u></i>';

                                $dept = \backend\models\ClassLevel::find()->where(['id'=>$studModel[0]['std_clslvl_relId']])->one();
                                echo "<br><b> Class Year: </b><i><u>".$dept->clslvl_desc.'</u></i>';
                                ?>
                            </div>
                            <div class="col-md-4">
                                <p class="bg-info">
                                    Your semester registration information and the courses offered will be displayed below. 
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="container-fluid" id="courses_to_enroll">
                                Loading your courses...
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-primary" style="display: none;" id="coff_enroll_course" >Enroll
                        </button>                        
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>

    <?=
    $this->registerJs(
        "
        enrolList = [];
        $('#coff_enroll_course').click(function(e){
            e.preventDefault();
            if(enrolList.length == 0){
                alert('Please select the courses you offer before submitting');
            }else{
                enrolList.sort(function(a,b){return a-b});
                cocatListString = JSON.stringify(enrolList);
                console.log('ENROL_LISTSTRING: '+cocatListString);
                console.log('Type of COCATLISTSTRING: '+typeof cocatListString);
                $('#coff_cocat_relIds_text').val(cocatListString);
                url = '".Yii::$app->urlManager->createUrl('semester-registration3/create')."';
                $.post(
                    url,
                    {cids:cocatListString.toString(),},
                    function(data){
                        console.log('Data: '+data);
                    });
                return true;
            }
        });

        window.addEnroll = function(cocatId){
            // Add/remove the id from the list
            if(jQuery.inArray(cocatId,enrolList) !== -1){ // if item is already selected, remove it because user deselected it.
                enrolList.splice($.inArray(cocatId,enrolList),1);
            }else{//going to add the item to the list.
                enrolList.push(cocatId);
            }
            console.log('Cocats List : '+enrolList);
            if (enrolList.length) {
                $('#coff_enroll_course').show();
            }else{
                    $('#coff_enroll_course').hide();
            }
        }
        url3 = '".Yii::$app->urlManager->createUrl('semester-registration3/courses')."';
            $.post(url3,{},function(data){
                console.log('Data returned: '+data);
                $('#courses_to_enroll').html(data);
                })
        ",
        $this::POS_READY);
    ?>
<!-- <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'semreg_stud_relId',
            'semreg_sem_relId',
            'semreg_coff_relId',
            'semreg_coff_relIds',
            // 'semreg_mark_obtained',
            // 'semreg_created_on',
            // 'semreg_created_by',
            // 'semreg_updated_on',
            // 'semreg_updated_by',
            // 'remark:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?> --></div>
