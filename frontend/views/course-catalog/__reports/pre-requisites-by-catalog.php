<?php

if (isset($catModel)) {
    $btn = Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar') ? "<button class='btn btn-xs btn-danger pull-right' onclick='laodAllMajorCourses({$catModel->id});return false;' title='Load all mojor courses for this program'>All Mojor Courses</button>" : '';
    $preqCourses = $catModel->getPreqsByCocat();
    if ($preqCourses) {
        echo "<div style='min-height: 450px; max-height: 450px; overflow:auto;'><table class='table table-condensed table-sm table-bordered table-hover' style='font-size:9pt;'>";
        echo "<caption style='color:#000; text-align:center;'>
                Pre-requisite course(s){$btn}
            </cation>";
        echo "<thead style='background-color: #8D0;'>
            <th>#</th>
            <th>Course Code</th>
            <th>Title</th>
            <th>Credit Hrs.</th>
            <th>Course Dept</th>
            <th>Catalog Entry</th>
            <th>Option</th>
        </thead><tbody>";
        // echo "";
        $count = 0;
        foreach ($preqCourses as $preqCourse) {
            $pCourse = $preqCourse->preqPreqCourseRel; //
            $catalogEntry = formatCatalogCourse($catModel->getCatalogEntry($preqCourse));
            $failedStudents = "<button class='btn btn-xs btn-danger' title='Failed students in this course' onclick='listFailedStudents({$preqCourse->preq_preq_course_relId});return false'>Failed Students</button>";
            $mngBtn = "<button class='btn btn-xs btn-warning' title='Manage course pre-rquisites' onclick='window.location.href=\"/course-prerequisit/index?CoursePrerequisitSearch[preqMainCourseRel.course_title]={$catModel->cocatCourseRel->course_title}\"'>Manage</button>";
            echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$pCourse->course_code}</td>            
                <td>{$pCourse->course_title}</td>            
                <td>{$pCourse->getCourseCredits()}</td>            
                <td>{$pCourse->courseDeptRel->dept_code}</td>            
                <td>{$catalogEntry}</td>            
                <td>{$failedStudents}{$mngBtn}</td>            
            </tr>";
        }
        echo "</tbody></table></div>";
    } else {
        echo "Nill {$btn}";
    }
} else {
    echo "No data found";
}

function formatCatalogCourse($catalogs)
{
    $reply = '';
    if ($catalogs) {
        foreach ($catalogs as $catalog) {
            $category = $catalog->courseCategoryRel ? "({$catalog->courseCategoryRel->catgeory_name})" : '-';
            $spec = $catalog->cocat_spec_relId ? "({$catalog->cocatSpecRel->spec_name})" : '';
            $reply .= $catalog->cocatDeptRel->dept_code . " $spec : " . $catalog->getCatalogSet() ." <b class='pull-right'> ". $category." </b><br>";
        }
    }

    return $reply;
}
