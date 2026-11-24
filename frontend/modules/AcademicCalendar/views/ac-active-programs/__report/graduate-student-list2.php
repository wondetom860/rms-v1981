<style type="text/css">
    thead {
        vertical-align: bottom;
        text-align: center;
    }

    thead>tr {
        text-align: center;
    }

    body>tr {
        font-size: 8pt;
    }

    thead>tr>th {
        text-align: center;
    }

    th.rotated-text {
        height: 250px;
        white-space: nowrap;
        padding: 5px !important;
        overflow-y: auto;
        word-break: inherit;
        width: 45px;
    }

    th.rotated-text>div {
        transform:
            translate(5px, 0px) rotate(-90deg);
        width: 35px;
        font-size: 8pt;
        text-align: center;
        height: 45px;
    }

    th.rotated-text>div>span {
        padding: 2px 5px;
    }

    th.rotated-text>div>small {
        padding: 1px 5px;
        display: block;
    }

    .table-caption {
        font-weight: bold;
        color: #000;
        text-align: center;
    }
</style>
<?php

use backend\models\Course;
use backend\models\CourseCatalog;
use backend\models\SemesterRegistration2;
use backend\models\SemesterRegistration2Detail;
use backend\models\Student;

if (isset($list)) {
    $catModel = new CourseCatalog();
    $courses = $list['all_courses'] ?? [];
    $semregs = $list['all_semregs'] ?? [];
    $one_srd2 = $list['one_srd2'] ?? [];
    // border=1 cellspacing=2 style='width: 100%'
    // class='table table-bordered table-condensed table-sm table-striped'  style='font-size: 9pt;'
    $course_code_column_header = getCourseCodeColumnHeader($courses, $one_srd2, $catModel);
    $header_courses = $course_code_column_header[1];
    $header_credits = $course_code_column_header[2];
    $course_code_column_header = $course_code_column_header[0];
    $count_courses = 2 * count($header_courses);
    $acy = $list['acadYear']->getAcHeader();
    $acp = $list['acp'] ?? null;
    $spec = $acp->specRel ? $acp->specRel->spec_name : '';
    echo "<table border=1 cellspacing=2 cellpad=2 style='width: 100%'>";
    echo "<caption class='table-caption'>
        <h4>{$acy} Graduate Class Grade Point Summery</h4>
        <small>
            Program: <u><stron>{$acp->programRel->program_name}({$acp->admissionRel->admission_name[0]})</strong></u>
            Department: <u><stron>{$acp->deptRel->dept_name}</strong></u>
            Specialiaztion: <u><stron>{$spec}</strong></u>
        <small>
    </caption>";
    echo "<thead style='background-color:rgb(228, 221, 221)'>
            <tr>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>#</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>REG. ID</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>NAME</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>GENDER</th>
                <th colspan={$count_courses} style='text-align: center;'>Courses</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom' title='Credit Hour Sum'>CrHrSum</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>GPS</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>CGPA</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>STATUS</th>
                <th rowspan=3 style='white-space:nowrap; padding: 1px 3px; vertical-align:bottom'>REMARK</th>
            </tr>
            <tr>
                {$course_code_column_header}
            </tr>
            <tr>
                {$header_credits}
            </tr>
        </thead><tbody>";

    $count = 0;
    $allStudents = $list['all_students'] ?? [];
    $all_students = array_unique(array_column($semregs, 'SEMREG_STD_RELID'));
    // var_dump($all_students);exit;
    
    foreach ($all_students as $index => $stdId) {
        $student = Student::findOne($stdId);
        $student->calculateGPAs();
        $lastSemreg = $student->getLastSemreg();
        $cgpa = $lastSemreg->semreg_last_ssn_relId ? sprintf("%0.3f", $lastSemreg->semreg_CGPA) : '-';

        $course_grades = getCoursegrades($student, $header_courses, $semregs);
        $crdtHrSum = 0;
        $last_status = '';
        if ($student->std_semester_status == 'ACTIVE') {
            $last_status = $lastSemreg->semreg_last_ssn_relId ? $lastSemreg->semregLastSsnRel->sssn_name : '-';
        } else {
            $last_status = $student->std_semester_status;
        }

        echo "<tr>
            <td>" . ++$count . "</td>
            <td>{$student->std_regId}</td>
            <td style='white-space:nowrap;'>{$student->getFullName()}</td>
            <td>{$student->stdUserRel->gender}</td>
            {$course_grades}
            <td style='text-align: center;'>{$cgpa}</td>
            <td style='text-align: center; white-space:nowrap;'>{$last_status}</td>
            <td style='text-align: center;'>-</td>
        </tr>";
    }
    echo "</tbody></table><br><br><br>";
} else {
    echo "<p class='bg-warning'>Invalid request<p>";
    return;
}

function getCoursegrades($student, $courses, $semregs)
{
    // $ll = 2*count($courses);
    // return "<td colspan='{$ll}'>Loading</td>";

    $std_semregs = array_filter($semregs, function ($sr) use ($student) {
        return $sr['SEMREG_STD_RELID'] == $student->id;
    });

    // $std_srd2s = 

    if ($std_semregs) {
        $courses_grade = '';
        $gps = 0;
        $creditHr_sum = 0;
        $equivalentCourseIds = [];
        foreach ($courses as $id => $course) {

            if (in_array($course['COURSEID'], $equivalentCourseIds)) {
                continue; // skip if already added
            }

            $course_srd2s = getCourseSrd2s($course, $student);

            if ($course_srd2s) {
                foreach ($course_srd2s as $srd22) {
                    // var_dump($srd22);exit;
                    $grade = NULL;
                    $courses_grade_inner = '';
                    $gps_inner = 0;
                    $credit_hr_sum_inner = 0;
                    foreach ($srd22 as $srd2) {
                        if ($grade == NULL) {
                            $grade = $srd2->srd2GradeRel;
                            $course2 = $srd2->srd2CourseRel;
                            if ($grade) {
                                $gp = sprintf("%0.2f", $course2->course_crdthr * $grade->gs_gradePoint);
                                $gps_inner = $gp;
                                $credit_hr_sum_inner = $course2->course_crdthr;
                                $ltr_grade = $grade->gs_ltr_grade;
                                $courses_grade_inner = "
                                    <td style='text-align: center;'>{$ltr_grade}</td>
                                    <td style='text-align: center;'>{$gp}</td>";
                            } else {
                                // check if enrolled equivalent course
                                $equivalentCourseSrd2s = getEquivalentCourseSrd2($srd2, $student);
                                if ($equivalentCourseSrd2s) {
                                    $grade = $equivalentCourseSrd2s->srd2GradeRel;
                                    $course2 = $equivalentCourseSrd2s->srd2CourseRel;

                                    $equivalentCourseIds[] = $course['COURSEID']; // add to equivalent course ids

                                    $gp = sprintf("%0.2f", $course2->course_crdthr * $grade->gs_gradePoint);
                                    $gps_inner = $gp;
                                    $credit_hr_sum_inner = $course2->course_crdthr;
                                    $ltr_grade = $grade->gs_ltr_grade;
                                    $courses_grade_inner = "
                                        <td style='text-align: center;'>{$ltr_grade}</td>
                                        <td style='text-align: center;'>{$gp}</td>";
                                } else {
                                    $courses_grade_inner = "<td style='text-align: center;background-color: #DCE;' colspan=2>-</td>";
                                }
                            }
                        } else {
                            if ($grade && $srd2->srd2GradeRel && ($grade->gs_gradePoint < $srd2->srd2GradeRel->gs_gradePoint)) {
                                $grade = $srd2->srd2GradeRel;
                                $course2 = $srd2->srd2CourseRel;
                                if ($grade) {
                                    $gp = sprintf("%0.2f", $course2->course_crdthr * $grade->gs_gradePoint);
                                    $gps_inner = $gp;
                                    $credit_hr_sum_inner = $course2->course_crdthr;
                                    $ltr_grade = $grade->gs_ltr_grade;
                                    $courses_grade_inner = "
                                        <td style='text-align: center;'>{$ltr_grade}</td>
                                        <td style='text-align: center;'>{$gp}</td>";
                                } else {
                                    // check if enrolled equivalent course
                                    $equivalentCourseSrd2s = getEquivalentCourseSrd2($srd2, $student);
                                    if ($equivalentCourseSrd2s) {
                                        $grade = $equivalentCourseSrd2s->srd2GradeRel;
                                        $course2 = $equivalentCourseSrd2s->srd2CourseRel;

                                        $equivalentCourseIds[] = $course['COURSEID']; // add to equivalent course ids

                                        $gp = sprintf("%0.2f", $course2->course_crdthr * $grade->gs_gradePoint);
                                        $gps_inner = $gp;
                                        $credit_hr_sum_inner = $course2->course_crdthr;
                                        $ltr_grade = $grade->gs_ltr_grade;
                                        $courses_grade_inner = "
                                        <td style='text-align: center;'>{$ltr_grade}</td>
                                        <td style='text-align: center;'>{$gp}</td>";
                                    } else {
                                        $courses_grade_inner = "<td style='text-align: center;background-color: #DCE;' colspan=2>-</td>";
                                    }
                                }
                            }
                        }
                    }
                    $courses_grade .= $courses_grade_inner;
                    $gps += $gps_inner ?? 0;
                    $creditHr_sum += $credit_hr_sum_inner ?? 0;
                }
            } else {
                $courses_grade .= "<td style='text-align: center; background-color: #DCE' colspan=2>-</td>";
            }
        }
        return $courses_grade .
            "<td style='text-align: right; padding-right: 8px;'>" . sprintf("%0.2f", $creditHr_sum) . "</td>" .
            "<td style='text-align: right; padding-right: 8px;'>" . sprintf("%0.2f", $gps) . "</td>";
    } else {
        return "<td colspan='" . (2 * count($courses)) . "'>No Semester registration record found</td>";
    }
}

function getEquivalentCourseSrd2($srd2, $student)
{
    $course_srd2 = NULL;
    $course2 = $srd2->srd2CourseRel;
    $equivalentCourseIds = $course2->getEquivalentCourseIds();
    if (!empty($equivalentCourseIds)) {
        $srd2s = SemesterRegistration2Detail::find()
            ->innerJoinWith('srd2SemregRel')
            ->where(['semreg_std_relId' => $student->id])
            ->andWhere(['srd2_course_relId' => $equivalentCourseIds])
            ->all();
        if ($srd2s) {
            $max_srd2 = $srd2s[0];
            foreach ($srd2s as $srd2) {
                if ($srd2->srd2GradeRel && $max_srd2->srd2GradeRel && ($srd2->srd2GradeRel->gs_gradePoint > $max_srd2->srd2GradeRel->gs_gradePoint)) {
                    $max_srd2 = $srd2;
                }
            }
            $course_srd2 = $max_srd2;
        }
    }
    return $course_srd2;
}

function getCourseSrd2s($course, $student)
{
    $course_srd2s = [];
    $srd2s = SemesterRegistration2Detail::find()
        ->innerJoinWith('srd2SemregRel')
        ->where(['semreg_std_relId' => $student->id])
        ->andWhere(['srd2_course_relId' => $course['COURSEID']])
        ->all();
    if ($srd2s) {
        $course_srd2s[] = $srd2s;
    }
    return $course_srd2s;
}
function getCourseCodeColumnHeader($all_courses, $srd2, $catModel)
{
    $course_code_column_header = '';
    $course_crdthr_column_header = '';
    $equivalented_course_ids = [];
    $header_courses = [];
    foreach ($all_courses as $i => $course) {
        if (in_array($course['COURSEID'], $equivalented_course_ids)) {
            continue; // skip if already added
        }
        $header_courses[] = $course;
        $course1 = Course::findOne($course['COURSEID']);
        $equivalentCourseIds = $course1->getEquivalentCourseIds();
        if ($equivalentCourseIds) {
            $equivalented_course_ids = array_merge($equivalented_course_ids, $equivalentCourseIds);
        }
        $elective = '';//is_elctive($srd2, $catModel) ? ' (Elective)' : '';
        $course_code_column_header .= "<th class='rotated-text'colspan='2' title='{$course['COURSETITLE']}'>
                <div>
                    <span>{$course['COURSECODE']}{$elective}</span>
                    <small>{$course['COURSETITLE']}</small>
                </div>
            </th>";
        $course_crdthr_column_header .= "<th style='text-align: center;' colspan='2' title='Credit Hour'>{$course1->getCourseCredits(1)}</th>";
    }
    return [$course_code_column_header,$header_courses,$course_crdthr_column_header];
}

function is_project($srd2, $catModel)
{
    foreach (getCatalogEntries($srd2, $catModel) as $cocat) {
        if ($cocat->cocat_courseProject == 1) {
            return true;
        }
    }
    return false;
}

function is_elctive($srd2, $catModel)
{
    foreach (getCatalogEntries($srd2, $catModel) as $cocat) {
        if ($cocat->cocat_courseelectives == 1) {
            return true;
        }
    }
    return false;
}

function getCatalogEntries($srd2, $catModel)
{
    $catalogCourses = $catModel->getCatalogCourse2($srd2->srd2GroupRel);
    $courseCatalogEntries = array_filter($catalogCourses, function ($tt) use ($srd2) {
        return $tt->cocat_course_relId == $srd2->srd2_course_relId;
    });

    return $courseCatalogEntries;
}

?>

<style>
    th,
    td {
        padding-top: 1px;
        padding-bottom: 2px;
        padding-left: 3px;
        padding-right: 4px;
    }
</style>