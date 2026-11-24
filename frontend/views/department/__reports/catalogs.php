<?php

/**
 * @var \backend\models\Department $model
 * @var \backend\models\CourseCatalog $catalog
 */

if (isset($model)) {
    $catalogEntries = $model->getCourseCatalogs()
        ->orderBy([
            'cocat_program_relId' => SORT_ASC,
            'cocat_clslvl_relId' => SORT_ASC,
            'cocat_sem_relId' => SORT_ASC,
            ])
        ->all();
    echo "<h4 style='text-align: center;'>Course Catalog Entries<button class='btn btn-xs btn-primary pull-right' onclick='showCatalogForm({$model->id});return false;'>New Catalog Entry</button></h4>";
    if ($catalogEntries) {
        echo "<div style='margin-bottom: 10px; max-height: 300px; overflow-y: auto;'>";
        echo "<table class='table table-sm table-bordered table-condensed table-hover' style='font-size: 12px'>";
        echo "<thead style='background-color: #7D9EC0; color: white;'>
                <th>#</th>
                <th>Course Code</th>
                <th>Title</th>
                <th>Credits</th>
                <th>Department</th>
                <th>Program</th>
                <th>Set for</th>
                <th>Entry type</th>
            </thead><tbody>";
        $count = 0;
        foreach ($catalogEntries as $catalog) {
            $course = $catalog->cocatCourseRel;
            echo "<tr>
                    <td>" . ++$count . "</td>
                    <td>{$course->course_code}</td>
                    <td>{$course->course_title}</td>
                    <td>{$course->getCourseCredits()}</td>
                    <td>{$course->courseDeptRel->dept_code}</td>
                    <td>{$catalog->cocatProgramRel->getProgramHeader()}</td>
                    <td>{$catalog->getCatalogSet()}</td>
                    <td>{$catalog->catalogEntryType}</td>
                </tr>";
        }
        echo "</tbody></table></div>";
    } else {
        echo '<p class="bg-warning">No Catalogs found for this department</p>';
    }
}

?>

<script>
    const showCatalogForm = (deptId) => {
        $('#modal').find('.modal-header').html('Create catalog entry for department');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['course-catalog/get-catalog-form']) ?>',
            type: 'POST',
            data: {deptId: deptId},
            success: function (data) {
                $('#modal').modal('show');
                $('#modalContent').html(data);
            }
        });
    }
</script>
