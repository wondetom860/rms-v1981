<?php
/** @var yii\web\View $this */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Attendance;
use kartik\date\DatePicker;
use backend\models\Course;
use backend\models\WGroup;
use common\models\User;

$this->title = 'Generate Attendance Report';
$this->params['breadcrumbs'][] = $this->title;
$this->params['courseId'] = $courseId;
$this->params['groupId'] = $groupId;
$model = new Attendance();
$modelCourse = new Course();
$modelGroup = new WGroup();
$modelUser = new User();
?>
<br>
<div class="container-fluid" style="background-color: white; padding: 4px;">
    <?php if (Yii::$app->session->hasFlash('message')) {
        ?>
    <div class="row">
        <div class="alert alert-warning  alert-dismissable">
            <?= Yii::$app->session->getFlash('message'); ?>
        </div>
    </div>
    <?php }else{?>

    <?php $form = ActiveForm::begin(['action'=>'/attendance-report/attendance-report-form']); ?>
    <div class="row">
        <div class="col-md-9">
            <h4 style="font-style: italic;">Fill the form below to generate attendance report </h4>
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

        <div class="col-md-2"><br>
            <div class="form-group" style="margin-top: 5px;">
                <?php echo Html::activeHiddenInput($model, "a_coff_relId",['id'=>'a_coff_relId_ID']);?>
                <?= Html::submitButton('Generate', ['class' => 'btn btn-success btn-flat','id'=>'sdjhfkgsfsg']) ?>
            </div>
       </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont('gsgdsajfgdsfgh')">Print<span class="glyphicon glyphicon-print"></span></i>
            <div class="container-fluid" id="gsgdsajfgdsfgh" style="border: 1px dashed black;">
                
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); 
}?>
</div>

<?php
$this->registerJs(
    "

    $('#sdjhfkgsfsg').click(function(event){
        event.preventDefault();
        $('#printIcon').hide();
        $('#gsgdsajfgdsfgh').html('Generating Student attendance report');
        form = $('#w0');
        // console.log(form.attr('action'));

        $.ajax({
            type:'POST',
            url:form.attr('action'),
            data:form.serialize(),
            success:function(data){
                try{
                    d = JSON.parse(data);
                    if(d[0] == 1){
                        $('.alert').hide();
                        $('#printIcon').show();
                        $('#gsgdsajfgdsfgh').html(d[1]);
                    }else{
                         $('#gsgdsajfgdsfgh').html(data);
                    }
                }catch(e){

                }
            },
            error:function(data){
                $('#gsgdsajfgdsfgh').html(data);
            }

        });


        // return false;
    });
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

    window.printCont = function(el){
        // console.log(el);

      // Add the print css to the table. with ID = reportTable
      //$('#reportTable').addClass('table table-bordered');
      $('#reportTable'). css({
        'background-color': \"yellowgreen\",
        \"font-weight\": \"bold\",
        'border':'1px solid black'
        });

        $('.non-print').hide();

      $('#reportTable td,th').css({
        \"font-weight\": \"normal\",
        'borderRight':'1px solid black',
        'borderBottom':'1px solid black'
        });
      $('#reportTable th').css({
        \"font-weight\": \"bold\"
        });

      // $('#reportTable tbody tr').css({
      //   'borderBottom':'1px solid black'
      //   });
      $('#studentSemesterStatusContainer').css({
        'borderTop':'2px solid black',
        'textAlign':'center'
        });

        var restorePage = $('body').html();
        var printContent = $('#gsgdsajfgdsfgh').html();
        $('body').html(printContent);
        window.print();
        $('body').html(restorePage);
        $('.non-print').show();
    }
    ",
    $this::POS_READY);
?>