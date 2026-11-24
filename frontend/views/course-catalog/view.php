<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** 
 * @var yii\web\View $this 
 * 
 * @var $model backend\models\CourseCatalog */

$this->title = "[{$model->cocatCourseRel->getCourseCredits()}] : " . $model->cocatCourseRel->course_title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Catalogs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-catalog-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(Yii::t('rms', 'Failed Students 2'), ['#'], [
                'class' => 'btn btn-primary btn-xs',
                'title' => 'List faield students in this course (Repeated >=1)',
                'onclick' => "listFailedStudents2({$model->cocat_course_relId},{$model->id});return false;"
            ]) ?>
            <?= Html::a(Yii::t('rms', 'Failed Students'), ['#'], [
                'class' => 'btn btn-warning btn-xs',
                'title' => 'List faield students in this course',
                'onclick' => "listFailedStudents({$model->cocat_course_relId},{$model->id});return false;"
            ]) ?>
            <?= Html::a(Yii::t('rms', 'See History'), ['/course/course-history'], [
                'class' => 'btn btn-info btn-xs',
                'target' => '_blank',
                'title' => 'See offering history of this course',
            ]) ?>

            <?= Html::a(Yii::t('rms', 'See More'), ['#'], [
                'class' => 'btn btn-success btn-xs',
                'onclick' => "showMore({$model->cocat_course_relId});return false;",
                'title' => 'See offering history of this course',
            ]) ?>
        </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            [
                'attribute' => 'cocat_program_relId',
                'value' => function ($model) {
                    return $model->cocatAdmissionRel->admission_name . ' ' . $model->cocatProgramRel->program_name;
                }
            ],
            [
                'attribute' => 'cocat_dept_relId',
                'value' => function ($model) {
                    return $model->cocatDeptRel->dept_name;
                }
            ],
            [
                'attribute' => 'cocat_clslvl_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return  $model->cocatClslvlRel ? $model->cocatClslvlRel->getClslvlDisp() : '-';
                }
            ],
            [
                'attribute' => 'cocat_sem_relId',
                'value' => function ($model) {
                    return  $model->cocatSemRel ? $model->cocatSemRel->semester_name : '-';
                }
            ],
            [
                'attribute' => 'cocat_spec_relId',
                'value' => function ($model) {
                    return  $model->cocatSpecRel ? $model->cocatSpecRel->spec_name : '-';
                }
            ],
            [
                'attribute' => 'ECTS',
                'value' => function ($model) {
                    return  "
                        Course ECTs: {$model->cocatCourseRel->course_ETCS}|
                        Calculated-1: {$model->gteCourseECTs()}|
                        Calculated-2: {$model->gteCourseECTs2()}
                    ";
                }
            ],
            [
                'attribute' => 'course_category_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $btnSetCategory = Html::button($model->course_category_relId ? 'Update' : 'Set Course Category', ['class' => 'btn btn-warning btn-xs pull-right', 'onclick' => "setCourseCategory({$model->id});return false;"]);
                    return  $model->course_category_relId ? $model->courseCategoryRel->catgeory_name . $btnSetCategory : $btnSetCategory;
                }
            ],
            [
                'attribute' => 'min_pass_grade_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $btnSetMinPassGrade = Html::button($model->min_pass_grade_relId ? 'Update' : 'Set Min Pass Grade', ['class' => 'btn btn-info btn-xs pull-right', 'onclick' => "setMinPassGrade({$model->id});return false;" ,'title' => 'If left blank, pass status will be determined by the grade obtained.']);
                    return  $model->minPassGradeRel ? $model->minPassGradeRel->gs_ltr_grade . $btnSetMinPassGrade : $btnSetMinPassGrade;
                }
            ],
            [
                'attribute' => 'Prerequisites',
                'label' => "Prerequisite Course(s)",
                'format' => 'raw',
                'value' => function ($model) {
                    return  $this->render('__reports/pre-requisites-by-catalog', ['catModel' => $model]);
                }
            ]
        ],
    ]) ?>

</div>

<script>
    const savePreqBatch = (modelId) => {
        if(__maj_courses_list.length){
            $.post('/course-catalog/create-preq-batch',{
                catalogId: modelId,
                majorCourses: __maj_courses_list.join(',')
            }).done((resp) => {
                if(resp == 1){
                    location.reload();
                }
            });
        }else{
            alert('Please select at least one course');
            return false;
        }
    }
    __maj_courses_list = [];
    const toggleAll999 = (vv) => {
        $.each($('.major_course_row'),function(c){
            id = this['id'];
            status = addToList(Number(id));
            $('#'+id).prop({'checked':(status == 1)});
        });
    }
    const addToList = (major_id) => {
        status = 0;
        if(jQuery.inArray(major_id,__maj_courses_list) !== -1){ // if item is already selected, remove it because user deselected it.
            __maj_courses_list.splice($.inArray(major_id,__maj_courses_list),1);
            status = 0;
        }else{//going to add the item to the list.
            __maj_courses_list.push(major_id);
            status = 1;
        }

        $('#selected_count').html(__maj_courses_list.length);
        return status;
    }
    const laodAllMajorCourses = (catalogId) => {
        
        $('#modal').find('.modal-header').html('All Major Courses for this program');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/course-catalog/get-all-major-courses', {
            id: catalogId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
        __maj_courses_list = [];
        $('#selected_count').html(__maj_courses_list.length);
        
    }
    const setCourseCategory = (catalogId) => {
        $('#modal').find('.modal-header').html('Set Course Category');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/course-catalog/set-category', {
            id: catalogId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const setMinPassGrade = (catalogId) => {
        $('#modal').find('.modal-header').html('Set Minimum Pass Grade');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/course-catalog/set-pass-grade', {
            id: catalogId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    // show-more
    const showMore = (courseId) => {
        $('#modal').find('.modal-header').html('Course Offering & Enrollment Detail');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/course/show-more', {
            id: courseId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
     const listFailedStudents2 = (courseid,ctalogid) => {
        $('#modal').find('.modal-header').html('Course Failed Students\' List (Repeated >=1)');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/semester-registration/get-course-failed-students2', {
            courseId: courseid,
            catalogid:ctalogid,
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const listFailedStudents = (courseid,ctalogid) => {
        $('#modal').find('.modal-header').html('Course Failed Students\' List');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/semester-registration/get-course-failed-students', {
            courseId: courseid,
            catalogid:ctalogid,
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>