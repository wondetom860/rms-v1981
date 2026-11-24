<?php

use backend\models\CourseOffering2Detail;


// $supervisorUsers = ArrayHelper::map(
//     Instructor::find()
//         ->innerJoinWith('instUserRel')
//         ->where([
//             'user.status' => User::STATUS_ACTIVE,
//             'user.activated' => 1
//         ])
//         ->orderBy([
//             'first_name' => SORT_ASC,
//             'middle_name' => SORT_ASC,
//             'last_name' => SORT_ASC
//         ])
//         ->all(),
//     'id',
//     function ($model) {
//         return $model->getFullName();
//     }
// );

// $standbysupervisorUserIds = $model->standbys ? json_decode($model->standbys, true) : [];

// $allUsers = Instructor::find()
//     ->innerJoinWith('instUserRel')
//     ->where([
//         'user.status' => User::STATUS_ACTIVE,
//         'user.activated' => 1
//     ])
//     ->andWhere(['not in', 'inst_user_relId', $standbysupervisorUserIds])
//     ->orderBy([
//         'first_name' => SORT_ASC,
//         'middle_name' => SORT_ASC,
//         'last_name' => SORT_ASC
//     ])
//     ->all()
$acadYear = $model->dateTimeSlotRel->examScheduleEntryRel->acadYearRel;
$offerings = CourseOffering2Detail::find()
    ->innerJoinWith('coff2detCoff2Rel')
    ->innerJoin('course', 'course.id=course_offering2_detail.coff2det_course_relId')
    ->where([
        'coff2_acadYear_relId' => $acadYear->schoolYearRel->id,
        'coff2_sem_relId' => $acadYear->semesterRel->id
    ])
    ->orderBy([
        'coff2_group_relId' => SORT_ASC,
        'course.course_dept_relId' => SORT_ASC,
        'course.course_code' => SORT_DESC,
    ])
    ->all();
?>

<form action="#">
    <div class="row">
        <div class="hidden">
            <input type="hidden" name="id" value="<?= $model->id ?>">
            <input type="hidden" name="offeringIds" id="offering_ids" value="<?= $model->offeringIds ?>">
        </div>
        <div class="col-md-12">
            <label for="supervisor_user_id" class="bg-info">Select Course <b>Offered in [<?= $acadYear->getAcHeader() ?>] | Exam Scehdule: <?= $model->getHeaderText()  ?></b></label>
            <div class="form-group" style="max-height: 400px; overflow:auto; padding: 3px;">
                <table class="table table-bordered table-sm table-condensed table-striped table-hover" style="font-size: 9pt;">
                    <tr style="background-color: #ACE">
                        <th>#</th>
                        <th>Course Dept.</th>
                        <th>Credits</th>
                        <th>Section</th>
                        <th>Students</th>
                        <th>Option</th>
                    </tr>
                    <tbody>
                        <?php
                        $count = 0;
                        foreach ($offerings as $offering) {
                            $course = $offering->coff2detCourseRel;
                            $gset = $offering->coff2detCoff2Rel->coff2GroupRel->getGsDisp();
                            $aStudents = $offering->getEnrollsCount();
                            $scheduledInAnotherOffering = $model->courseAvailableInAnotherSchedule2($offering->id);
                            $sectionCourseCollision = $model->checkForSectionCourseCollision($offering->id);
                            // student course schedule collision
                            $studentCourseCollision = $model->checkForStudentCourseCollision($offering->id);
                            $courseBlockedByAnotherCourse = $model->checkForCourseBlockedByAnotherCourse($offering->id);
                            // $option = $scheduledInAnotherOffering ? :
                            if($sectionCourseCollision) {
                                $option = "<small class='badge bg-warning' title='Other course offered for the section is scheduled in this time slot.'>Scheduled</small>";
                            }else if($scheduledInAnotherOffering){
                                $option = "<small class='badge bg-info' title='Course scheduled in another date time slot.'>Scheduled</small>";
                            }else if($studentCourseCollision){
                                $option = "<small class='badge bg-info' title='Student has another course in the same date time schedule.'>Scheduled</small>";
                            }else if($courseBlockedByAnotherCourse){
                                $option = "<small class='badge bg-warning' title='This slot is ocuppied by another course offered to the same section.'>Scheduled</small>";
                            }else{
                                $option = "<input type='checkbox' id='cat__{$offering->id}' onchange='addToList({$offering->id}); return false;'/>";
                            }
                            echo "<tr>
                                <td>" . ++$count . "</td>
                                <td>{$course->courseDeptRel->dept_code}</td>
                                <td>{$course->getCourseCredits()}</td>
                                <td>{$gset}<b>[{$offering->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}]</b></td>
                                <td style='text-align: center;'>{$aStudents}</td>
                                <td style='text-align: center;'>{$option}</td>
                            
                            </tr>";
                            // echo "<option value='{$course->id}' title='Course department : '>[{$course->courseDeptRel->dept_code}] - [{$course->getCourseCredits()}] : ($gset)</option>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row bg-info">
        <div class="col-md-12"><br>
            <div class="form-group" style="margin-top: 10px;">
                Selected Offerings: <span id="selected_offerings">0</span>
                <button class="btn btn-xs btn-primary pull-right" onclick="saveSelectedOfferings();return false;">Save</button>
            </div>
        </div>
    </div>
</form>

<script>
    __offerings = [];
    window.addToList=  (cofferingId)=>{
        r = 0;
        if(jQuery.inArray(cofferingId,__offerings) !== -1){ 
            // if item is already selected, remove it because user deselected it.
            __offerings.splice($.inArray(cofferingId,__offerings),1);
            r= 0;
        }else{//going to add the item to the list.
            __offerings.push(cofferingId);
            r= 1;
        }
        $('#offering_ids').val(JSON.stringify(__offerings));
        $('#cat__'+cofferingId).prop({'checked':(r == 1)});
        $("#selected_offerings").text(__offerings.length);
        // console.log(__offerings);
    }
    function saveSelectedOfferings() {
        if (confirm('Save selected offerings?')) {
            var id = $('input[name="id"]').val();
            // var __offerings = $('#offering_detail_id').val();
            $.ajax({
                url: '/ExamSchedule/ex-exam-room-slot/add-offerings?id=' + id,
                type: 'POST',
                data: {
                    id: id,
                    offeringIds: JSON.stringify(__offerings)
                },
                success: function(data) {
                    if (data == 1) {
                        // $.pjax.reload({container: '#pjax-container'});
                        // ShowAddSupervisorForm(id);
                        window.location.reload();
                    }
                }
            });
        }
    }
</script>