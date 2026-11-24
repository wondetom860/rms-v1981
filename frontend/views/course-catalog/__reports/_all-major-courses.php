<?php

if (isset($majorCourses)) {
    if ($majorCourses) {
        $selectAll = "<input type='checkbox' onchange='toggleAll999();false' title='(Un)Check All'/>";
        echo "<div style='min-height: 450px; max-height: 450px; overflow:auto;'><table class='table table-condensed table-sm table-bordered table-hover' style='font-size:9pt;'>";
        echo "<caption style='color:#000; text-align:center;'>
                All Major Courses of the Program
            </cation>";
        echo "<thead style='background-color: #8D0;'>
            <th>#</th>
            <th>Course Code</th>
            <th>Title</th>
            <th>Credit Hrs.</th>
            <th>Course Dept</th>
            <th>Catalog Entry</th>
            <th>Option{$selectAll}</th>
        </thead><tbody>";
        // echo "";
        $count = 0;
        foreach ($majorCourses as $majorCourse) {
            $catalogEntry = formatCatalogCourse([$majorCourse]);
            $course = $majorCourse->cocatCourseRel;
            $toggleChkbx = "<input type='checkbox' class='major_course_row pull-right' id='{$course->id}' onchange='addToList({$course->id});return false;' />";
            // $failedStudents = "<button class='btn btn-xs btn-danger' title='Failed students in this course' onclick='listFailedStudents({$preqCourse->preq_preq_course_relId});return false'>Failed Students</button>";
            // $mngBtn = "<button class='btn btn-xs btn-warning' title='Manage course pre-rquisites' onclick='window.location.href=\"/course-prerequisit/index?CoursePrerequisitSearch[preqMainCourseRel.course_title]={$catModel->cocatCourseRel->course_title}\"'>Manage</button>";
            echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$course->course_code}</td>            
                <td>{$course->course_title}</td>            
                <td>{$course->getCourseCredits()}</td>            
                <td>{$course->courseDeptRel->dept_code}</td>            
                <td>{$catalogEntry}</td>            
                <td>{$toggleChkbx}</td>            
            </tr>";
        }
        echo "</tbody></table>
        <p class='text-muted label bg-info'>Seleted: <span id='selected_count'>0</span></p>
        <button class='btn btn-xs btn-primary pull-right' title='Set selected courses as pre-requisite' onclick='savePreqBatch({$model->id});return false;'>Save</button>
        </div>";
    } else {
        echo "Nill";
    }
} else {
    echo "No data found";
}

function formatCatalogCourse($catalogs)
{
    $reply = '';
    if ($catalogs) {
        foreach ($catalogs as $catalog) {
            $category = $catalog->courseCategoryRel ? "({$catalog->courseCategoryRel->catgeory_name[0]})" : '-';
            $spec = $catalog->cocat_spec_relId ? "({$catalog->cocatSpecRel->spec_name})" : '';
            $reply .= $catalog->cocatDeptRel->dept_code . " $spec : " . $catalog->getCatalogSet() ." <b class='pull-right'> ". $category." </b><br>";
        }
    }

    return $reply;
}
