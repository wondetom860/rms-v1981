<?php 
//use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use backend\models\Course;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\Attendance;
use frontend\models\WGroup;
use dosamigos\datepicker\DatePicker;

$this->params['groupId']= $groupId;
$this->params['groupName']= $groupName;
$this->params['studentID']= $studentID;
$this->params['studentName']= $studentName;
$this->params['courseId']= $courseId;
$this->params['courseName']= $courseName;
$this->params['count']= $count;
?>

<script type="text/javascript">
    function checkall(source){
        checkbox=document.querySelectorAll('input[type="checkbox"]');
        for (var i = 0; i <checkbox.length; i++) {
            if(checkbox[i]!=source){
                checkbox[i].checked=source.checked;
            }
        }
    }
</script>

<?php
 $model=new Attendance();
 // Yii::$app->session->getFlash('success');
 ?>
 <?php $form =ActiveForm::begin() ?>
 <div class="row">
  <div class="col-md-6">
  <div class="panel panel-default">
    <div class="panel-heading" style="background:#6495ED;color: white;">Student Attendance Form</div>

    <div class="row">
    <div class="col-md-1">SN</div>
    <div class="col-md-7">Course Name</div>
    <div class="col-md-2">Group</div>
    <div class="col-md-2">Action</div>
    </div>
    <div class="row">
    
        <div class="col-md-1">
            <?php
        for ($i=1;$i<$count;$i++)
              { 
             echo $i."</br>";
              }  
             ?>
        </div>
        <div class="col-md-7">
        <?php
                 for ($i=1; $i <count($courseName) ; $i++) { 
                     echo $courseName[$i].'<br>';
                 }
                 ?>
        <?php //echo 
       //  $form->field($model, 'a_course_relId')->dropdownList(ArrayHelper::map(Course::find()->where(['id'=>$courseId])->all(),'id','course_title'),['prompt'=>'Select course']);  
        ?>
        </div>
        <div class="col-md-2">
        <?php
                 for ($i=1; $i <count($groupName) ; $i++) { 
                     echo $groupName[$i].'<br>';
                 }
                 ?>
        
        <?php //echo $form->field($model, 'a_group_relId')->dropdownList(ArrayHelper::map(WGroup::find()->where(['id'=>$groupId])->all(),'id','group_name'),['prompt'=>'Select group','onChange'=>'loadStudents($(this).val())']);  ?>
        </div>
        <div class="col-md-2" ><?php
        for ($i=1;$i<$count;$i++){ ?>
        <!--<input type='checkbox' onclick="checkall(this)"> -->
        <?=  Html::Button('Students', ['class' => 'btn btn-success btn-xs']).'<br>'; ?>
         <?php echo "</br>";
              }   ?>
        <?php //echo $form->field($model, 'a_createdAt')->widget(DatePicker::className(), [
                    //         'attribute'=>'a_createdAt',
                    //        // 'type' => DatePicker::TYPE_INPUT,
                    //         'options' => ['placeholder' => 'enter class date ...'],
                    //         'clientOptions' => [
                    //             'autoclose' => true,
                    //            // 'orientation' => 'top left',
                    //             'format' => 'yyyy-mm-dd',
                    //             'todayHighlight' => true,
                    //             'daysOfWeekDisabled' => [0, 6],
                    //             //'multidate' => true,
                    //             //'multidateSeparator' => ' ; ',
                    //         ]
                    // ]); 
                    ?>
        </div>
    </div>
    <!--
    <div class="row">
    <div class="col-md-12">
    <table class="table table-bordered table-striped">
        <tr>
            <th>SN</th>
            <th>Student Name</th> -->
           <!-- <th> <input type='checkbox' onclick="checkall(this)"> Status</th> -->
      <!--  </tr>
        <?php 
        //foreach ($stdList as $stdlist) {
         ?>
        <tr>
            <td>
                 <?php // for ($i=1;$i<=$count;$i++){
                 //     echo $i."</br>";
                 // }
                ?>
            </td>
            <td><?php 
             //  foreach ($studentName as $stdName){
                //  echo $stdName;
                //  echo "</br>";
                // }
                // for ($k=1;$k<count($studentName);$k++) {
                //  echo $studentName[$k]."</br>";
                // }
                ?>
            </td>
            <td>
            <?php //for ($i=1;$i<count($studentID);$i++){ ?>
                <!--<input type="checkbox" name="id[]" onChange="addtoList('<?php // $studentID[$i]; ?>')"> 
            <?php //echo "</br>";
              //}   
            ?>
            </td>
        </tr>
        <?php //}?>
        
    </table>
     </div>
    </div>
    
    -->    <!-- <input type="hidden" value="" id=""> -->
    <?php //Html::submitButton('Save', ['class' => 'btn btn-success','id'=>'saveList']) ?>
    <?php ActiveForm::end() ?>
  </div>
 </div>
</div>
<?php

$this->registerJs(
    "
    window.loadStudents=function(groupId){
        courseID=$('#attendance-a_course_relid').val();
           $.post(
                '/attendance/student-list',
                {
                    courseID:courseID,
                    groupID :groupId ,
                    },
        ).done(function(data){
            console.log(data);
        });
    }
    // $('#saveList').on('click',function(e){
        
        
    // });
    $('#saveList').click(function(e){
        e.preventDefault();
        stdList = JSON.stringify(presentList); //JSON encode.
        console.log(stdList);
        CID=$('#attendance-a_course_relid').val();
        GID=$('#attendance-a_group_relid').val();
        GDate=$('#attendance-a_createdat').val();
        // alert(CID);
            $.post(
                '/attendance/create-attendance',
                {
                    courseId:CID,
                    groupId :GID ,
                    date:GDate,
                    studentsList:stdList,
                    },
        ).done(function(data){
            console.log(data);
        });
        return false;
    });

    presentList = [];
    window.addtoList=function(studentId){
        // console.log(studentId);
        if(jQuery.inArray(studentId,presentList) !== -1){ // if item is already selected, remove it because user deselected it.
            presentList.splice($.inArray(studentId,presentList),1);
        }else{//going to add the item to the list.
            presentList.push(studentId);
        }
        console.log(presentList);
    }

    ",
    $this::POS_READY);
?>
