<?php

use backend\models\CourseCatalog;

if (isset($semregModel) && count($semregModel) > 0) {
    $std = $semregModel[0]->semregStdRel;
    $spec = $std->std_spec_relId ? $std->stdSpecRel->spec_name : '-';
    $catModel = new CourseCatalog();
    $gradeTable =
        "<div class='container-fluid' style='text-align:center; margin-top:10px;' >
                    <h4 style='color: #000; font-weight: bold;'>Course Completion Report</h4>
            </div>";
    $gradeTable .= "<table class='table table-hover table-sm table-condensed table-bordered'>";
    $gradeTable .= "
                    <caption style='text-align:center; font-weight: bold; color: #000; font-size: 10px;'>                    
                        <p class='bg-default'> 
                            <b>Student Name:</b><u><i>{$std->getFullName()}</u></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <b>Reg.No.:</b> <u><i>{$std->std_regId}</u></i><br>
                            <b>Program:</b><u><i>{$std->stdProgRel->program_name}</u></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <b>Admission:</b><u><i>{$std->stdAdmissionRel->admission_name}</u></i><br>
                            <b>Department :</b><u><i>{$std->stdDeptRel->dept_name}</u></i>&nbsp;&nbsp;&nbsp;
                            <b>Specialization :</b><u><i>{$spec}</u></i>
                        </p>
                    </caption>";
    $gradeTable .= "<thead style='background-color:#ace;'>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Acad. Year</th>
            <th>CrdtHr</th>
            <th>Grade</th>
            <th>Grd. Pt.</th>
        </thead><tbody>";

    // array_filter($result, function ($r) use ($value) {
    // return $r['ACADEMICYEAR'] == $value;
    // });

    // student may leave a course un-enrolled in diffrent cases.
    // do the check by filtering all the course catalog of departments the student is assigned in so far.
    $major_trs = "";
    $common_trs = "";
    $major_total_credits = 0;
    $major_gpa_sum = 0;

    $common_total_credits = 0;
    $common_gpa_sum = 0;
    $count = 0;

    $major_repeated_gps = 0;
    $common_repeated_gps = 0;

    $major_project_credits = 0;
    $common_project_credits = 0;
    foreach ($semregModel as $semreg) {
        $repeated_gps = 0;
        $srd2s = $semreg->getSemesterRegistration2Details()->andWhere([
            'srd2_corptd_flag' => 0
        ])->all();
        if ($srd2s && count($srd2s) > 0) {
            foreach ($srd2s as $srd2) {
                $ltrGrade = $srd2->srd2_grade_relId == null ? '-' : strtoupper($srd2->srd2GradeRel->gs_ltr_grade);
                $gps = $srd2->srd2_grade_relId == null ? '-' : sprintf("%.2f", ($srd2->srd2GradeRel->gs_gradePoint * $srd2->srd2CourseRel->course_crdthr));

                // If any correction is made on the grade for the course, take the one with better grade point.
                $repEntries = $srd2->getRepeteEntries();
                if ($repEntries && count($repEntries) > 0) {
                    $repeated_gps = $srd2->srd2GradeRel ? ($srd2->srd2GradeRel->gs_gradePoint * $srd2->srd2CourseRel->course_crdthr) : 0; //repeated grade point must be deducted
                    $srd2 = $repEntries[0];
                    $ltrGrade = $srd2->srd2_grade_relId == null ? '-' : strtoupper($srd2->srd2GradeRel->gs_ltr_grade);
                    $gps = $srd2->srd2_grade_relId == null ? '-' : sprintf("%.2f", ($srd2->srd2GradeRel->gs_gradePoint * $srd2->srd2CourseRel->course_crdthr));
                }

                $rptdFlag = $srd2->srd2_corptd_flag == 1 ? "*" : '';
                $title = $srd2->srd2CourseRel->course_title;

                $title = str_replace("'", "&#39;", $title);
                $title = str_replace('"', "&#34;", $title);
                $title = str_replace("`", "&#96;", $title);

                $title = str_replace(";", "&#59;", $title);
                $title = str_replace(":", "&#58;", $title);

                $title = str_replace("(", "&#40;", $title);
                $title = str_replace(")", "&#41;", $title);

                $title = is_project($srd2, $catModel) ? "<b style='font-style: italic;'>{$title}</b>" : $title;

                if (is_major($srd2, $catModel)) {
                    // $count++;
                    $major_trs .= "<tr>";
                    $major_trs .= "<td>" . $srd2->srd2CourseRel->course_code . "</td>";
                    $major_trs .= "<td>" . $title . "{$rptdFlag}</td>";
                    $major_trs .= "<td>{$semreg->semregAcadYearRel->sch_year_desc}-{$semreg->semregSemesterRel->semester_name}</td>";
                    $major_trs .= "<td>" . $srd2->srd2CourseRel->getCourseCredits() . "</td>";
                    $major_trs .= "<td style='text-align: center;'>{$ltrGrade}</td>";
                    $major_trs .= "<td style='text-align: right; padding-right: 8px;'>{$gps}</td>";
                    $major_trs .= "</tr>";
                    $major_total_credits += $srd2->srd2_grade_relId ? $srd2->srd2CourseRel->course_crdthr : 0;
                    $major_gpa_sum += $srd2->srd2_grade_relId ? $gps : 0;
                    $major_repeated_gps += $repeated_gps;

                    $major_project_credits += (is_project($srd2, $catModel) && $srd2->srd2_grade_relId) ? $srd2->srd2CourseRel->course_crdthr : 0;
                } else {
                    $common_trs .= "<tr>";
                    $common_trs .= "<td>" . $srd2->srd2CourseRel->course_code . "</td>";
                    $common_trs .= "<td>" . $title . "{$rptdFlag}</td>";
                    $common_trs .= "<td>{$semreg->semregAcadYearRel->sch_year_desc}-{$semreg->semregSemesterRel->semester_name}</td>";
                    $common_trs .= "<td>" . $srd2->srd2CourseRel->getCourseCredits() . "</td>";
                    $common_trs .= "<td style='text-align: center;'>{$ltrGrade}</td>";
                    $common_trs .= "<td style='text-align: right; padding-right: 8px;'>{$gps}</td>";
                    $common_trs .= "</tr>";

                    $common_total_credits += $srd2->srd2_grade_relId ? $srd2->srd2CourseRel->course_crdthr : 0;
                    $common_gpa_sum += $srd2->srd2_grade_relId ? $gps : 0;
                    $common_repeated_gps += $repeated_gps;

                    $common_project_credits += (is_project($srd2, $catModel) && $srd2->srd2_grade_relId) ? $srd2->srd2CourseRel->course_crdthr : 0;
                }
            }
        } else {
            echo "<p class='bg-warning'>No record found.</p>";
        }
    }

    $major_gpa_sum -= $major_repeated_gps;
    $common_gpa_sum -= $common_repeated_gps;
    $major_total_credits -= $major_project_credits;
    $common_total_credits -= $common_project_credits;

    $major_gpa = $major_total_credits == 0 ? "-" : sprintf("%0.2f", ($major_gpa_sum / ($major_total_credits)));

    echo $gradeTable;
    echo "<tr>
            <td colspan=7 style='font-weight: bold; text-align: center; background-color: #7D9'>Major Courses</td>
        </tr>{$major_trs}
        ";
    $g_total_credits =  $common_total_credits + $major_total_credits;
    $g_total_gps = $common_gpa_sum + $major_gpa_sum;
    $cgpa = $g_total_credits == 0 ? '-' : (sprintf("%0.2f", ($g_total_gps / $g_total_credits)));
    if ($common_total_credits > 0) {
        echo "
            <tr style='font-weight: bold;'>
                <td colspan=4 style='text-align: right'>Sub Total</td>
                <td style='text-align: center'>{$major_total_credits}</td>
                <td colspan=2 style='text-align: right;padding-right: 8px;'>{$major_gpa_sum}</td>
            </tr>
            <tr style='font-weight: bold;'>
                <td colspan=4 style='text-align: right'>Major GPA</td>
                <td colspan=3 style='text-align: center; padding-right: 8px; text-decoration: underline; font-style: italic;'>{$major_gpa}</td>
            </tr>";
        echo "<tr>
            <td colspan=7 style='font-weight: bold; text-align: center; background-color: #7D9'>Common & Supportive Courses</td>
        </tr>{$common_trs}
        <tr style='font-weight: bold;'>
            <td colspan=4 style='text-align: right'>Sub Total</td>
            <td style='text-align: center'>{$common_total_credits}</td>
            <td colspan=2 style='text-align: right;padding-right: 8px;'>{$common_gpa_sum}</td>
        </tr>";
    }

    echo "<tr style='font-weight: bold;'>
            <td colspan=4 style='text-align: right'>Grand Total</td>
            <td style='text-align: center'>{$g_total_credits}</td>
            <td colspan=2 style='text-align: right;padding-right: 8px;'>{$g_total_gps}</td>
        </tr>
        <tr style='font-weight: bold;'>
            <td colspan=4 style='text-align: right'>CGPA</td>
            <td colspan=3 style='text-align: center; padding-right: 8px; text-decoration: underline; font-style: italic;'>{$cgpa}</td>
        </tr>
    ";

    echo "</tbody></table>";
} else {
    echo "<p class='bg-warning'>No record found.</p>";
}

function is_major($srd2, $catModel)
{
    foreach (getCatalogEntries($srd2, $catModel) as $cocat) {
        if ($cocat->cocat_courseMajor == 0) {
            return false;
        }
    }
    return true;
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

function calculateMajorGPA($srd2s, $catModel)
{
    $majors = array_filter($srd2s, function ($srd2) use ($catModel) {
        return is_major($srd2, $catModel);
    });
}

function getCatalogEntries($srd2, $catModel)
{
    $catalogCourses = $catModel->getCatalogCourse2($srd2->srd2GroupRel);
    $courseCatalogEntries = array_filter($catalogCourses, function ($tt) use ($srd2) {
        return $tt->cocat_course_relId == $srd2->srd2_course_relId;
    });

    return $courseCatalogEntries;
}
