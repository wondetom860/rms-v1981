<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\ThesisTitle */

$this->title = "Project Course title submission status: " . $model->ttStdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Thesis Titles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="thesis-title-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'tt_srd2_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getSrd2DetailInfo();
                }
            ],
            [
                "attribute" => 'tt_std_relId',
                'value' => function ($model) {
                    return $model->ttStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'tt_title:ntext',
                'format' => 'raw',
                'value' => function ($model) {
                    if (file_exists('uploads/thesisTitles/' . $model->tt_title)) {
                        return file_get_contents('uploads/thesisTitles/' . $model->tt_title);
                    } else {
                        return "<span style='color:red; font-style: italic;'>Title not found.</span>";
                    }
                }
            ],
            [
                'attribute' => 'tt_approved',
                'value' => function ($model) {
                    return $model->tt_approved ? "Approved" : "Not approved yet";
                }
            ],
            [
                'attribute' => 'tt_hod_approved',
                'format' => 'raw',
                'value' => function ($model) {
                    $btn = "";
                    if (Yii::$app->user->can('DepartmentHead')) {
                        $btn = $model->tt_hod_approved ?
                            ($model->tt_graded ? " (Thesis graded.)" : "<button class='btn btn-xs btn-warning pull-right' onclick='changeStatus({$model->id},2); return false;'>Cancel Approve</button>")
                            :
                            "<button class='btn btn-xs btn-success pull-right' onclick='changeStatus({$model->id},1); return false;'>Approve</button>";
                    }
                    return $model->tt_hod_approved ? "Approved{$btn}" : "Not approved yet{$btn}";
                }
            ],

            'tt_graded',
        ],
    ]) ?>

</div>
<script type="text/javascript">
    const changeStatus = (tId, option) => {
        if (confirm('Are you sure to change status of thesis title')) {
            $.post('/thesis-title/change-status', {
                tkeyId: tId,
                option: option
            }).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                } else {
                    alert(resp);
                }
            });
        }
    }
</script>