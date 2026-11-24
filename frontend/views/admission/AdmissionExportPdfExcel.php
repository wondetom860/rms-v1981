<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use yii\widgets\Pjax;
	use yii\widgets\ActiveForm;
	/** @var yii\web\View $this */
	/* @var $searchModel backend\models\AdmissionSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */

	$this->title = Yii::t('rms', 'Admissions');
	$this->params['breadcrumbs'][] = $this->title;


	Pjax::begin(); 
	$org = common\models\AuthOrg8n::find()->asArray()->one();
	$model->sort = false;
	$dispColumn = false;
	if($type == 'Excel') {
		$dispColumn = true;
		echo "<meta http-equiv=\"Content-type\" content=\"text/html;charset=utf-8\" />";
		echo "<table><tr> <th colspan='10'><h3>".$org['name']."</h3> </th> </tr> </table>";
	}
	?>    
	<?= GridView::widget([
        'dataProvider' => $model,
        // 'dataProvider' => $model,
        'layout' => '{items}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'admission_name',
            [
                'attribute'=>'admission_status',
                'value'=>function($model){
                        return $model->admission_status == 1?'ACTIVE':'NOT ACTIVE';
                    },
            ],
            [
                'attribute'=>'admission_created_by',
                'value'=>function($model){

                    return \backend\models\User::findOne(['id'=>$model->admission_created_by])->username;
                },
            ],
            [
                'attribute'=>'admission_created_at',
                'value'=>function($model){
                    return date_format(date_create($model->admission_created_at),'d M,Y');
                }
            ],
            // 'admission_updated_by',
            // 'admission_updated_at',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?>