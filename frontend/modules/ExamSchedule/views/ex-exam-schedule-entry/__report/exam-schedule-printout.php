<?php
if (isset($model)) {
    // $this->report
    $dateTimeScehdules = $model
        ->getExDateTimeSchedules()
        ->orderBy([
            'date' => SORT_ASC,
            "shift" => SORT_ASC
        ])
        ->all();
    // echo "<h3 style='text-align: center: '></h3>";
    if ($dateTimeScehdules) {
        $sd = date('dMY', $model->start_date);
        $ed = date('dMY', $model->end_date);
        echo "<table class='table table-sm table-condensed table-bordered'>
            <caption style='caption-side: top; text-align: center;color: #000;'>
                <h4>
                    Exam Schedule Entry: {$model->acadYearRel->getAcHeader()}<br>
                    <small>Start Date: {$sd} | End Date: {$ed}</small>
                </h4>
        </caption>
        ";
        echo "<thead>
                <th>Date</th>
                <th>Exam Room</th>
                <th>Course</th>
                <th>Sections</th>
                <th>Students</th>
                <th>Remark</th>
            </thead><tbody>";
        foreach ($dateTimeScehdules as $dateTimeSchedule) {
            $datee = $dateTimeSchedule->getDateScheduled();
            $time = $dateTimeSchedule->getScheduleTime();
            $examRooms = format_display_exam_rooms($dateTimeSchedule);

            echo "<tr>
                <td class='vertical-td' rowspan={$examRooms[0]}>{$time}<br>{$datee}</td>
                {$examRooms[1]}
                ";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='bg-warning'>Date time not scheduled yet.</p>";
    }
} else {
    echo "<p class='bg-danger'>Invalid Request</p>";
}

function format_display_exam_rooms($dateTimeSchedule)
{
    $examRoomsCount = 0;
    $roomTrs = "";
    $examSlots = $dateTimeSchedule->exExamRoomSlots;
    if ($examSlots) {
        foreach ($examSlots as $examRoom) {
            $sections = $examRoom->getExExamOfferingDetails()->all();
            $sectionsFormated = getFormatedSections($sections, $examRoom);
            $examRoomsCount += $sectionsFormated[0];
            // getClassRoomRel
            $roomTrs .= "
            <td rowspan={$sectionsFormated[0]} style='vertical-align: middle; text-align: center;'>{$examRoom->classRoomRel->room}</td>
            {$sectionsFormated[1]}";
        }

        return [$examRoomsCount, $roomTrs];
    } else {
        return [1, "<td colspan=5><p class='bg-warning'>No Exam room selected.</p></td>"];
    }
}

function getFormatedSections($sections, $examRoom)
{
    $formatedSections  = "";
    $rowsCount = 0;
    if ($sections) {
        foreach ($sections as $section) {
            $rowsCount++;
            $gset = $section->coff2detCoff2Rel->coff2GroupRel->getGsDisp();
            $stds = $examRoom->getExExaminees()
                ->andWhere(['offering_detail_relId' => $section->id])
                ->count();
            $formatedSections .= "
                <td>{$section->coff2detCourseRel->getCourseCredits()}</td>
                <td>{$gset}</td>
                <td style='text-align: center;'>{$stds}</td>
                <td style='text-align: center;'></td>
            </tr>
            ";
        }
    } else {
        return [1, "<td colspan=4><p class='bg-warning'>No section selected.</p></td>"];
    }

    return [$rowsCount, $formatedSections];
}
?>

<style>
    .vertical-td {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        width: 23px;
        border-left: 1px solid black;
        vertical-align: middle;
        text-align: center;
    }
</style>