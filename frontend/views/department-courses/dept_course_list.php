<?php

use backend\models\SemesterRegistration2Detail;

if (isset($stdModel)) {
    $dept = $stdModel->stdDeptRel;
    $catalogs = $dept->getCourseCatalogs()
        ->andWhere([
            'cocat_admission_relId' => $stdModel->std_admission_relId,
            'cocat_program_relId' => $stdModel->std_prog_relId,
            'cocat_status' => 1
        ])
        ->orderBy([
            'cocat_clslvl_relId' => SORT_ASC,
            'cocat_sem_relId' => SORT_ASC,
        ])
        ->groupBy(['cocat_course_relId'])
        ->all();
    if ($catalogs) {
        echo "<table class='table table-sm table-condensed table-hover table-bordered'>";
        echo "<tr style='background-color: #167896'>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Credit Hrs.</th>
                <th>Category</th>
                <th>Pre-Requisite</th>
                <th>Dept.</th>
                <th>Yr./Sem.</th>
                <th>Remark</th>
            </tr>
            <tbody>";
        $count = 0;
        foreach ($catalogs as $catalog) {
            $prereqs = format_course_prereqs($catalog->getPreqsByCocat());
            $course = $catalog->cocatCourseRel;
            $category = $catalog->course_category_relId ? $catalog->courseCategoryRel->catgeory_name : '-';
            // $grade = get_grade($course, $stdModel);
            echo "<tr>
                    <td style='vertical-align: middle;'>" . ++$count . "</td>
                    <td style='vertical-align: middle;'>{$course->course_code}</td>
                    <td style='vertical-align: middle;'>{$course->course_title}</td>
                    <td style='vertical-align: middle;'>{$course->getCourseCredits()}</td>
                    <td style='vertical-align: middle;'>{$category}</td>
                    <td style='max-height: 50px; overflow: scrol;'>{$prereqs}</td>
                    <td style='vertical-align: middle;'>{$course->courseDeptRel->dept_name}</td>
                    <td style='vertical-align: middle;'>{$catalog->getCatalogSet()}</td>
                    <td></td>
                </tr>";
        }

        echo "<tbody></table>";
    } else {
        echo "<p class='bg-warning'>Catalog Not found</p>";
        return;
    }
} else {
    echo "<p class='bg-warning'>Invalid access</p>";
    return;
}

function get_grade($c, $s)
{
    $srd2 = SemesterRegistration2Detail::find()
        ->innerJoinWith('srd2SemregRel')
        ->innerJoin('course', 'course.id=semester_registration_2_detail.srd2_course_relId')
        ->innerJoin('grade_scale', 'grade_scale.id = semester_registration_2_detail.srd2_grade_relId')
        ->where([
            'semreg_std_relId' => $s->id,
            'srd2_course_relId' => $c->id,
            'grade_scale.gs_pass_status' => 1,
            // 'srd2_pass_status' => 1
        ])
        ->one();
    return $srd2 ? ($srd2->srd2_evaluationCompleted == 1 ? $srd2->srd2GradeRel->gs_ltr_grade : "NE") : '-';
}

function format_course_prereqs($p_catalogs)
{
    $p_coursecodes = [];
    if ($p_catalogs) {
        if (count($p_catalogs) > 2) {
            $p_coursecodes[] = $p_catalogs[0]->preqPreqCourseRel->course_code;
            $p_coursecodes[] = "<a href='/course-catalog/view?id={$p_catalogs[0]->id}' target='_blank'> <i>and " . (count($p_catalogs) - 1) . ' others.</i></a>';
        } else {
            foreach ($p_catalogs as $p_catalog) {
                $p_coursecodes[] =  $p_catalog->preqPreqCourseRel->course_code;
            }
        }
    }

    return !empty($p_coursecodes) ? implode("<br>", $p_coursecodes) : "-";
}
