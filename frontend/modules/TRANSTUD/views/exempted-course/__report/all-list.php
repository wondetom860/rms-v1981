<?php
/**
 * 
 * @var frontend\modules\TRANSTUD\models\ExemptedCourse $model
 */
if (isset($courses) && isset($model)) {
    echo "<div style='max-height: 389px; overflow: auto; font-size: 8.5pt;'><table class='table table-condensed table-striped table-bordered'>";
    echo "<caption style='text-align: center;'><strong>Add Equivalent Courses for {$model->course_name} ({$model->course_code})</strong></caption>";
    echo "<thead style='background-color: #7d7d7d;'>
            <th>#</th>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Course Credit</th>
            <th>Department</th>
            <th>Entry Type</th>
            <th>Option</th>
        </thead><tbody>";
    $count = 0;
    $eqCourses = json_decode($model->equivalent_course_ids, true);
    foreach ($courses as $course) {
        if ($eqCourses && in_array($course->id, $eqCourses)) {
            continue; // Skip already selected courses
        }
        $entryType = $course->course_entry_type == 1 ? 'Regular' : 'Transfer';
        $btn = '-';
        if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            $btn = "<input type='checkbox' title='Add as equivalent course' onchange='addEquivalent({$course->id});return false;'>";
        }
        echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$course->course_code}</td>
                <td><a href='/course/view?id={$course->id}' target='_blank'>{$course->course_title}</a></td>
                <td>{$course->getCourseCredits()}</td>
                <td>{$course->courseDeptRel->dept_code}</td>
                <td>{$entryType}</td>
                <td style='text-align:center;'>{$btn}</td>
            </tr>";
    }
    echo "</tbody></table>
    
    </div><hr>
    <div>
        <span class='pull-right' style='text-align:center;padding: 3px;'>Total: <i id='fhsjfjhd'>0</i></span>
        <button class='btn btn-primary btn-xs pull-right' onclick='saveEqivCourse(); return false;' title='Save equivalent course(s).'>Save</button>
        
    </div>
    <br><br>
    ";
} else {
    echo "<p class='bg-warning'>Course List empty</p>";
}


?>

<script>

    window.saveEqivCourse = () => {
        if (__list.length < 1) {
            alert('No course selected');
            return;
        }
        $.post('<?= Yii::$app->urlManager->createUrl(['/TRANSTUD/exempted-course/save-equivalent']) ?>', {
            courseId: <?= $model->id ?>,
            list: __list,
        }).done((resp) => {
            alert(resp);
            // location.reload();
        });
    }
    window.addEquivalent = (courseId) => {
        let total = parseInt($("#fhsjfjhd").text());
        if ($(`input[type='checkbox'][onchange='addEquivalent(${courseId});return false;']`).is(':checked')) {
            total += 1;
            __list.push(courseId);
        } else {
            total -= 1;
            __list.splice($.inArray(courseId,__list),1);
        }
        console.log(__list);
        $("#fhsjfjhd").text(total);
    }
    __list = [];
    $("#fhsjfjhd").text(0);
</script>