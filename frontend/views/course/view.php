<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View  $this
 * @var backend\models\Course  $model
 */

$this->title = $model->course_title . "({$model->course_code})";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-view">

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
        </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'course_code',
            'course_title',
            [
                'attribute' => 'course_crdthr',
                'value' => function ($model) {
                    return $model->getCourseCredits() . "[ETCS={$model->course_ETCS}]";
                }
            ],
            [
                'attribute' => 'course_dept_relId',
                'value' => function ($model) {
                    return $model->courseDeptRel->dept_name;
                },
            ],
            [
                'attribute' => 'Catalogs',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('history/more-de-catalog', ['course' => $model]);
                },
            ],
            [
                'attribute' => 'Equivalent Course(s)',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('history/course-equivalents', ['course' => $model]);
                },
            ],
            [
                'attribute' => 'course_created_by',
                'value' => function ($model) {
                    return $model->courseCreatedBy->username;
                },
            ],
            [
                'attribute' => 'entry_type',
                'value' => function ($model) {
                    return $model->getCourseEntryType();
                }
            ],
            [
                'attribute' => 'course_type',
                'format' => 'raw',
                'value' => function ($model) {
                    $btnChange = "<button class='btn btn-xs btn-warning pull-right' title='Change Course Type' onclick='loadCourseTypes({$model->id});return false;'><span class='glyphicon glyphicon-edit'></span></button>";
                    $type = $model->getCourseTypeName();
                    return $type . " " . $btnChange;
                },
                'filter' => $model->getCourseTypeOptios(),
            ],
            [
                'attribute' => 'course_created_at',
                'value' => function ($model) {
                    return date_format(date_create($model->course_created_at), 'd M, Y');
                }
            ],
            [
                'attribute' => 'course_updated_by',
                'value' => function ($model) {
                    return $model->courseUpdatedBy->first_name;
                },
            ],
            [
                'attribute' => 'course_updated_at',
                'value' => function ($model) {
                    return date_format(date_create($model->course_updated_at), 'd M, Y');
                }
            ],

        ],
    ]) ?>

</div>

<script>
    const loadCourseTypes = (courseId) => {

        $('#modal').find('.modal-header').html('Set/Change Course Type');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/course/get-course-types', {
            courseId: courseId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const changeCourseType = (courseId) => {
        //    display modal to accept new course type

        if (newType !== null && [1, 2, 3, 4].includes(parseInt(newType))) {
            // Send AJAX request to update course type

            $.post('/course/change-type', {
                id: courseId,
                course_type: newType,
                _csrf: '<?= Yii::$app->request->csrfToken ?>'
            }).done((resp) => {
                if (resp == 1) {
                    location.reload(); // Reload the page to see changes
                } else {
                    alert('Error updating course type: ' + resp);
                }
            })

            //     fetch('/course/change-type', {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json',
            //             'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            //         },
            //         body: JSON.stringify({id: courseId, course_type: newType})
            //     })
            //     .then(response => response.json())
            //     .then(data => {
            //         if (data.success) {
            //             // alert('Course type updated successfully.');
            //             location.reload(); // Reload the page to see changes
            //         } else {
            //             alert('Error updating course type: ' + data.message);
            //         }
            //     })
            //     .catch(error => {
            //         console.error('Error:', error);
            //         alert('An error occurred while updating the course type.');
            //     });
            // } else if (newType !== null) {
            //     alert('Invalid input. Please enter a number between 1 and 4.');
        }
    };
</script>