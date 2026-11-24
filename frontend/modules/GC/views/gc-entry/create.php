<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcEntry */

$this->title = Yii::t('rms', 'Create GC Entry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'GC Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gc-entry-create">

    <h3>
        <?= Html::encode($this->title) ?>
        <p class="pull-right">
            <button title="Import from GC academic programs" class="btn btn-xs btn-primary" onclick="importFromGcAcademicProgram();return false;">Import from ACADEMIC PROGRAMS</button>
        </p>
    </h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<script>
    const importfromacp = function(acp_id) {
        $.post('/GC/gc-entry/import-from-acp', {
            acp_id: acp_id
        }).done((resp) => {
            if (resp == 1) {
                window.location.href = window.location;
            } else {
                alert(resp);
            }
        });
    }
    const importFromGcAcademicProgram = function() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['gc-entry/import-from-active-programs']) ?>',
            type: 'GET',
            dataType: 'html',
            success: function(data) {
                $('#modal').find('.modal-header').html('Graduate Students from Active Program');
                $('#modalContent').css({
                    'text-align': 'left',
                    'margin-top': '0px'
                });
                $('#modalContent').html('Loading form...');
                $('#modelContent').addClass('table-responsive');
                $('#modalContent').css('overflow', 'auto');
                $('#modalContent').css('height', '500px');
                $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
                $('#modal').modal('show');
                $('#modalContent').html(data);
            }
        });
    }
</script>