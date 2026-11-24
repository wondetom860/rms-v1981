<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudent */
$modelUser = $model->gstdUserRel;
$this->title = $model->gstdUserRel->first_name . ' '. $model->gstdUserRel->middle_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduate Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduate-student-view">
    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row" style="border-bottom: 1px solid black;">
        <div class="col-md-12">
            <p class="pull-right">
                <button class="btn btn-info btn-md" onclick="showPointHistory(<?= $model->id ?>);return false;">Point History</button>
                <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>
    </div>
    <div class="container"><br>
        <div class="row">
            <div class="col-md-3" style="background-color: #ffe;">
                <center><br><br><br>
                    <img src="/uploads/<?= $modelUser->photo_num ?>" alt="photo" class="img-circle" alt="Profile Image" height="200" width="180">
                    <p class="bg-primary"><?= $modelUser->first_name .' '. $modelUser->middle_name .' '. $modelUser->last_name ?></p>
                </center>
            </div>
            <div class="col-md-5">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute'=>'gstd_user_relId',
                            'value'=>function($model){
                                return $model->gstdUserRel->first_name. ' '. $model->gstdUserRel->middle_name . ' '. $model->gstdUserRel->last_name;
                            }
                        ],

                        'gstd_profession_category',
                        'gstd_rank',
                
                        [
                            'attribute'=>'gstd_prog_relId',
                            'value'=> function($model){
                                return $model->gstdAdmRel->admission_name .' ' . $model->gstdProgRel->program_name ;
                            } 
                        ],
                        [
                            'attribute'=>'gstd_dept_relId',
                            'value'=> function($model){
                                return $model->gstdDeptRel->dept_name ;
                            } 
                        ],
                        [
                            'attribute'=>'gstd_spec_relId',
                            'value'=> function($model){
                                return $model->gstdSpecRel->spec_name;
                            } 
                        ],
                        [
                            'attribute'=>'gstd_sponsorship_relId',
                            'value'=> function($model){
                                return $model->gstdSponsorshipRel->schType_name;
                            }
                        ],
                        
                        'gstd_prev_CGPA',
                        'gstd_enterance_exam',
                    ],
                ]) ?>
            </div>
            <div class="col-md-3">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        
                        'gstd_adm_year',
                        'gstd_grad_year',
                        
                        [
                            'attribute'=>'gstd_CGPA',
                            'value'=>function($model){
                                return $model->getGraduationCGPA();
                            }
                        ],
                        [
                            'attribute'=>'gstd_date_of_graduation',
                            'value'=>function($model){
                                return date_format(date_create($model->gstd_date_of_graduation),'M d, Y');
                            }
                        ],
                        [
                            'attribute'=>'gstd_doc_collected',
                            'value'=>function($model){
                                return $model->gstd_doc_collected == 1?'YES':'NOT YET';
                            }
                        ],
                        [
                            'attribute'=>'gstd_doc_collected_at',
                            'visible'=> $model->gstd_doc_collected == 1,
                            'value'=>function($model){
                                return date_format(date_create($model->gstd_doc_collected_at),'M d, Y');
                            }
                        ],

                        [
                            'attribute'=>'gsdt_prepared_by',
                            'visible'=> $model->gstd_doc_collected == 1,
                            'value'=>function($model){
                                return $model->gsdtPreparedBy ? $model->gsdtPreparedBy->first_name .' '. $model->gsdtPreparedBy->middle_name:'NOT SET';
                            }
                        ],
                        [
                            'attribute'=>'gstd_doc_signed',
                            'visible'=> $model->gstd_doc_collected == 1,
                            'value'=>function($model){
                                return $model->gstd_doc_signed == 1? 'YES':'NOT SIGNED';
                            }
                        ],
                        [
                            'attribute'=>'gstd_doc_signed_by',
                            'visible'=> $model->gstd_doc_collected == 1,
                            'value'=>function($model){
                                return $model->gstdDocSignedBy ? $model->gstdDocSignedBy->first_name .' '. $model->gstdDocSignedBy->middle_name:'NOT SET';
                            }
                        ],
                        [
                            'attribute'=>'gstd_doc_signed_at',
                            'visible'=> $model->gstd_doc_collected == 1,
                            'value'=>function($model){
                                return date_format(date_create($model->gstd_doc_signed_at),'M d, Y');
                            }
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    gstdList = [];
    window.showPointHistory = (gstdId)=>{
        gstdList.push(gstdId);
        myWindow = window.open(
          '/graduate-student/get-point-history?sfsgfgsfjk=sfjkhsjfdgh&fdfhsgh='+JSON.stringify(gstdList)+'&hfggdjhjhd=fsghd', 
          'title=Graduate Student Point History', 
          'height=600,width=1200');

        // myWindow.print();
        // return false;
    }
</script>
