<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\WithdrawalReason;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use backend\models\Course;
/** @var yii\web\View $this */
/* @var $model frontend\models\StudentWithdrawalHistory */
/* @var $form yii\widgets\ActiveForm */
 //$this->params["studentId"] = $studentId;
 $this->params["CourseCode"] = $CourseCode;
 $this->params["CourseTitle"] = $CourseTitle;
 $this->params["CourseCrHr"] = $CourseCrHr;
 $this->params["Grade"] = $Grade;
 $this->params["Points"] = $Points;
 $this->params["firstName"] = $firstName;
 $this->params["middleName"] = $middleName;
 $this->params["lastName"] = $lastName;
 
 $SGPA_11=0;$SGPA_12=0;$CrHr_11=0;$S_points_11=0;$C_points_11=0;
 // first year first semester
 $CGPA_11=0;$CGPA_12=0;$CrHr_12=0; $S_points_12=0;$C_points_12=0;
 // first year second semester
// $i=1;

?>
 <div class="student-withdrawal-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //echo $form->field($model, 'swh_student_relId')->textInput() ?>

    <?php //echo $form->field($model, 'swh_academicyear_relId')->textInput() ?>

    <?php //echo $form->field($model, 'swh_semester_relId')->textInput() ?>
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-6">
        <h3 style="font-family: Times New Roman">Student Name:<?= $firstName.' '.$middleName.' '.$lastName?> </h3></div>
      </div>
      <div class="row">
        <div class="col-md-6">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                    <th colspan="5"> <p style="text-align: center;">2011/2012 1st Semester [1st Year]</p></th>
                    </tr>
                   <tr>
                        <th>Code</th>
                        <th>Course Title</th>
                        <th>Cr.Hr</th>
                        <th>Grade</th>
                        <th>Points</th>
                    </tr>
                </thead>
                    <?php for ($i=1;$i<=5;$i++) {
                      $S_points_11=$S_points_11+$CourseCrHr[$i]*$Points[$i];
                      $CrHr_11+=$CourseCrHr[$i]; // First year credit hour
                       ?>
                    <tr>
                        <td><?= $CourseCode[$i] ?></td>
                        <td><?= $CourseTitle[$i]?></td>
                        <td><?= $CourseCrHr[$i] ?></td>
                        <td><?= $Grade[$i] ?></td>
                        <td><?= $CourseCrHr[$i]* $Points[$i]?></td>
                    </tr>
                    <?php } ?>
                     <tr>
                        <td rowspan="2">
                        Status:<?php if(($S_points_11/$CrHr_11)<1.25)
                                {
                                    echo "Dismisal";
                                }
                                elseif (($S_points_11/$CrHr_11)>=1.25 && ($S_points_11/$CrHr_11)<1.5) {
                                    echo "Warnning";
                                }else  {
                                    echo "Promoted";
                                }
                             ?>
                        </td>
                        <td>SGPA=<?=round(($S_points_11/$CrHr_11),2) ?></td>
                        <td><?=$CrHr_11?></td>
                        <td></td>
                        <td><?=$S_points_11?></td>
                    </tr>
                     <tr>
                        <td>CGPA=<?=  round((($S_points_11+$C_points_11)/$CrHr_11),2); ?></td>
                        <td><?= $CrHr_11+$CrHr_12;?></td>
                        <td></td>
                        <td><?= $S_points_11 + $C_points_11 ?></td>
                    </tr>
            </table>
        </div> 
        <?php $sum=0;
             $CreditHour=0;
             ?>
        <div class="col-md-6">
              <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                    <th colspan="5"> <p style="text-align: center;">2012/2013 2nd Semester [1st Year]</p></th>
                    </tr>
                   <tr>
                        <th>Code</th>
                        <th>Course Title</th>
                        <th>Cr.Hr</th>
                        <th>Grade</th>
                        <th>Points</th>
                    </tr>
                </thead>
                    <?php for ($i=6;$i<=10;$i++) {
                       $S_points_12=$S_points_12+$CourseCrHr[$i]* $Points[$i];
                        $CrHr_12+=$CourseCrHr[$i];  
                        ?>
                    <tr>
                        <td><?= $CourseCode[$i] ?></td>
                        <td><?= $CourseTitle[$i]?></td>
                        <td><?= $CourseCrHr[$i] ?></td>
                        <td><?= $Grade[$i] ?></td>
                        <td><?= $CourseCrHr[$i]*$Points[$i] ?></td>
                    </tr>
                    <?php } ?>
                     <tr>
                        <td rowspan="2">
                        Status:<?php if(($S_points_12/$CrHr_12)<1.25)
                                {
                                    echo "Dismisal";
                                }
                                elseif (($S_points_12/$CrHr_12)>=1.25 && ($S_points_12/$CrHr_12)<1.5) {
                                    echo "Warnning";
                                }else  {
                                    echo "Promoted";
                                }
                             ?>
                        </td>
                        <td>SGPA=<?=round(($S_points_12/$CrHr_12),2) ?></td>
                        <td><?=$CrHr_12?></td>
                        <td></td>
                        <td><?=$S_points_12?></td>
                    </tr>
                     <tr>
                        <td>CGPA=<?=round((($S_points_11 + $C_points_11+$S_points_12)/($CrHr_11+$CrHr_12)),2)?></td>
                        <td><?= $CrHr_12;?></td>
                        <td></td>
                        <td><?= $S_points_11 + $C_points_11+$S_points_12?></td>
                    </tr>
            </table>
        </div>
    </div>
    <div class="row">
    <div class="col-md-4">
        <?php //echo $form->field($model, 'swh_student_relId')->textInput() ?>
        <?php //echo $form->field($model, 'swh_academicyear_relId')->textInput() ?>
        <?php //echo $form->field($model, 'swh_semester_relId')->textInput() ?>
        <?php //echo $form->field($model, 'swh_withdrawalreason_relId')->textInput(); ?>  
        <?= $form->field($model, 'swh_request_status')->dropDownList(['option'=>'approve request','1'=>'Approve','-1'=>'Reject']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'swh_comment')->textarea(['rows' => 6]) ?>
    </div>
    </div>
  
    <?php //echo $form->field($model, 'swh_request_status')->textInput() ?>

    <?php //echo $form->field($model, 'swh_comment')->textarea(['rows' => 6]) ?>

    <?php //echo $form->field($model, 'swh_createdBy')->textInput() ?>

    <?php //echo $form->field($model, 'swh_updatedBy')->textInput() ?>

    <?php //echo $form->field($model, 'swh_createdAt')->textInput() ?>

    <?php //echo $form->field($model, 'swh_updatedAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Approve', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
