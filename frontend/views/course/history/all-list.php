<?php

if (isset($courses) && isset($main_course_id)) {
    echo "<div style='max-height: 389px; overflow: auto; font-size: 8.5pt;'><table class='table table-condensed table-striped table-bordered'>";
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
    foreach ($courses as $course) {
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
        <span class='pull-right' style='text-align:center;'>Total: <i id='fhsjfjhd'>0</i></span>
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
        $.post('<?= Yii::$app->urlManager->createUrl(['course/add-equivalent']) ?>', {
            courseId: <?= $main_course_id ?>,
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