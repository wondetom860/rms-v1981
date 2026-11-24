<?php
/** @var yii\web\View $this */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\Attendance;
use kartik\date\DatePicker;
use backend\models\Course;
use backend\models\WGroup;
use common\models\User;

// $this->title = 'Generate Report';
// $this->params['breadcrumbs'][] = $this->title;
// $this->params['courseId'] = $courseId;
// $this->params['groupId'] = $groupId;
// $model = new Attendance();
// $modelCourse = new Course();
// $modelGroup = new WGroup();
// $modelUser = new User();
?>
<br>
<div class="container-fluid" style="background-color: white; padding: 4px;">

    <?php $form = ActiveForm::begin(['action'=>'/attendance-report/attendance-report-form']); ?>
    <div class="row">
        <div class="col-md-9">
            <h3 style="font-style: italic;">Fill the form below to generate attendance report </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'courseId')->dropdownList([], ['prompt' => 'Select course', 'onchange' => 'loadGroups()']);
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'groupId')->dropdownList([], ['prompt' => 'Select group']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fromDate')->widget(DatePicker::className(), [
                'pluginOptions' => [
                    'autoClose' => true,
                    'format' => 'yyyy-m-dd',
                ],
            ]);
            ?>
        </div>
        <div class="col-md-2">
            <?=
                $form->field($model, 'toDate')->widget(DatePicker::className(), [
                    'pluginOptions' => [
                        'autoClose' => true,
                        'format' => 'yyyy-m-dd',
                    ],
                ]);
                ?>
        </div>
        <div class="col-md-2"><br>
            <div class="form-group" style="margin-top: 5px;">
                <?php echo Html::activeHiddenInput($model, "a_coff_relId",['id'=>'a_coff_relId_ID']);?>
                <?= Html::submitButton('Generate', ['class' => 'btn btn-success btn-flat']) ?>
            </div>
       </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(
    "
    window.loadGroups = function(){

        coffDeId = $('#attendance-courseid :selected').val();
        gsetId = $('#attendance-courseid :selected').data('gsetid');
        gname = $('#attendance-courseid :selected').data('gname');
        courseId = $('#attendance-courseid :selected').data('course');

        $('#a_coff_relId_ID').val(courseId);
        console.log(courseId,gsetId);

        optv = '<option value=\"\"> - </option><option value=\"'+gsetId+'\">'+gname+'</option>';

        // $('#attendance-groupid').val(gsetId);
        $('#attendance-groupid').html(optv);
    }

    window.loadCourses = function(){
        $.post('/attendance/course-list',{rrrrrrr:1}).done(function(data){
            $('#attendance-courseid').html(data);
        });
    }
    loadCourses();
    ",
    $this::POS_READY);

?>
