<?php

use backend\models\Course;

if (isset($course)) {
    echo "<h4 style='text-align: center;'>Equivalent/Exempted Courses<button class='btn btn-xs btn-primary pull-right' onclick='addExemptedCourse({$course->id})' title='Add equivalent course(s).'>Add equivalent Course(s)</button></h4>";
    if ($course->course_equivalet === null) {
        if ($course->course_entry_type == 2) {
            echo "<p class='bg-warning'>No exempted course</p>";
        } else {
            echo "<p class='bg-info'>Equivalent course not set.</p>";
        }
    } else {
        echo "<table class='table table-condensed table-striped table-bordered' style='font-size: 9pt;'>";
        echo "<thead style='background-color: #8B0;'>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Course Credit</th>
                <th>Department</th>
                <th>Entry Type</th>
                <th>Option</th>
            </thead><tbody>";
        $count = 0;
        foreach (json_decode($course->course_equivalet, true) as $i => $equivalent) {
            $eCourse = Course::findOne($equivalent);
            $entryType = $eCourse->course_entry_type == 1 ? 'Regular' : 'Transfer';
            $btn = '-';
            if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
                $btn = "<button class='btn btn-danger btn-xs' title='Remove equivalent course?' onclick='removeEquivalent({$course->id},{$equivalent});return false;'>Remove</button>";
            }
            echo "<tr>
                    <td>" . ++$count . "</td>
                    <td>{$eCourse->course_code}</td>
                    <td>{$eCourse->course_title}</td>
                    <td>{$eCourse->getCourseCredits()}</td>
                    <td>{$eCourse->courseDeptRel->dept_name}</td>
                    <td>{$entryType}</td>
                    <td>{$btn}</td>
                </tr>";
        }
        echo "</tbody></table>";
    }
}
?>

<script>
    window.removeEquivalent = (courseId,equivalentCourseId) => {
        if (!confirm('Are you sure you want to remove this course as equivalent course?')) {
            return;
        }
        $.post('<?= Yii::$app->urlManager->createUrl(['course/remove-equivalent']) ?>', {
            courseId: courseId,
            equivalentCourseId: equivalentCourseId,
        }).done((resp) => {
            alert(resp);
            location.reload();
        });
    }
    window.addExemptedCourse = (courseId) => {
        // let course = prompt('Enter course code(s) separated by comma(,).');
        $('#modal').find('.modal-header').html('List of course to set as equivalent course');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('<?= Yii::$app->urlManager->createUrl(['course/list-all']) ?>', {
            courseId: courseId,
        }).done((resp) => {
            $('#modalContent').html(resp);
        });
    }
</script>