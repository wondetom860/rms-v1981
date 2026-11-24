<?php

if (isset($model)):
?>
    <div class="reassessor-mark-form">
        <div class="col-row" style="font-size: 9pt;">
            <div class="col-md-12 hidden">
                <input type="text" value="<?= $model->id ?>">
            </div>
            <table class="table table-condensed table-bordered table-sm table-hover">
                <thead>
                    <th>Assessment Instance</th>
                    <th>Original Mark</th>
                    <th>Weight</th>
                    <th>Changed To</th>
                </thead>
                <?php foreach ($model->reassessorMarks as $ra): ?>
                    <tr>
                        <td><?= $ra->rasImaRel->ima_instanceName ?></td>
                        <td><?= $ra->ras_original_mark ?></td>
                        <td><?= $ra->rasImaRel->ima_possiblePoint ?></td>
                        <td><input 
                            class="form-control input_mark" 
                            id="<?= $ra->id ?>" 
                            type="number" 
                            data-ravalue="<?= $ra->id ?>" 
                            value="<?= $ra->ras_changed_to ?>" 
                            min="0" 
                            max="<?= $ra->rasImaRel->ima_possiblePoint ?>">
                        </td>
                    </tr>
                <?php endforeach ?>
                <tr>
                    <td colspan="3">
                        <button class="btn btn-xs btn-primary pull-right" onclick="submitMark();return false;">Submit</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
<?php endif ?>

<script>
    marks = {};
    window.submitMark = () => {
        // valid = true;
        jQuery.each($(".input_mark"), function(c) {
            ima = this['id'];
            value = $("#" + ima).val();
            marks[ima] = value;
        });
        $.post('/Reassessment/reassessor-mark/create-batch', {
            marks: JSON.stringify(marks)
        }).done((resp) => {
            if (resp == 1) {
                window.location.href = window.location;
            } else {
                alert(resp);
            }
        });
        // console.log(marks);
    }
</script>