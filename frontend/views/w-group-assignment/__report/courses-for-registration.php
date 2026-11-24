<?php

if (isset($model) && !empty($model)) {
    $gset = $model->gasGsetRel;
    $coffs = $gset->getCourseOfferings()->all();
    $student = $model->gasStdRel;

    if ($coffs) {
        foreach ($coffs as $coff) {
            $coffdets = $coff->courseOffering2Details;
            if ($coffdets) {
                echo "<table class='table table-sm table-condensed table-bordered table-hover'>";
                echo "<caption style='text-align:center; font-weight:bold; color: #000;'>Register for course on behalf of <u>{$student->getFullName()}</u></caption>";
                echo "<thead style='background-color: #8D9;'>
                        <th>#</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Credit Hrs</th>
                        <th>Course Dept.</th>
                        <th>Option</th>
                    </thead><tbody>";
                $count = 0;
                foreach ($coffdets as $coff2det) {
                    // echo "<p>{$coff2det->coff2detCourseRel->getCourseCredits()}</p>";
                    $course = $coff2det->coff2detCourseRel;
                    $option = "<input type='checkbox' style='text-align: center;' onchange='addToList({$coff2det->id});return false;' >";
                    echo "<tr>
                            <td>" . ++$count . "</td>
                            <td>{$course->course_code}</td>
                            <td>{$course->course_title}</td>
                            <td>{$course->getCourseCredits()}</td>
                            <td title='{$course->courseDeptRel->dept_name}'>{$course->courseDeptRel->dept_code}</td>
                            <td style='text-align: center;'>{$option}</td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='bg-warning'>Course not offered.</p>";
            }
        }
    } else {
        echo "<p class='bg-warning'>Course not offered.</p>";
    }
} else {
    echo "<p class='bg-warning'>Invalid request</p>";
}
