<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Offering Details');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-detail-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'coff2det_coff2_relId',
            'coff2det_cocat_relId',
            'coff2det_course_relId',
            'coff2det_inst_relId',
            'coff2det_TA_relId',
            // 'coff2det_created_by',
            // 'coff2det_created_at',
            // 'coff2det_updated_at',
            // 'coff2det_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
