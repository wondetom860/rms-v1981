<?php 
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
?>
<!-----Start current batch fees details----->

<div class="row">
  <div class="col-xs-12">
	<h4>	
		<i class="fa fa-etb"></i> <?php echo Yii::t('rms', 'Student Fees Details'); ?>
	</h4>
  </div><!-- /.col -->
</div>

<div class="box box-solid">
    <div class="box-body table-responsive no-padding">
		<?php 
		$currFeesData = new ActiveDataProvider([
			'query' => $modelStudent->getSelfSponsoredPaymentHistories(),
			'sort' => [
				'defaultOrder' => [
					'ssph_acadYear_relId' => SORT_DESC, 
					'ssph_sem_relId' => SORT_DESC, 
				],
			],
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		// $currFeesData->sort = false;
		echo GridView::widget([
			'dataProvider' => $currFeesData,
			'layout' => "{items}\n{pager}",
			'showOnEmpty'=>true,
			'emptyText'=>'No fees data available.',
			'columns' => [
				['class' => 'yii\grid\SerialColumn'],
				[
					'attribute' => 'ssph_acadYear_relId',
					'value' => 'ssphAcadYearRel.sch_year_desc',
				],[
					'attribute' => 'ssph_sem_relId',
					'value' => 'ssphSemRel.semester_name',
				],
				[
					'attribute' => 'ssph_pymntRsn_relId',
					'value' => function($model){
						return $model->ssphPymntRsnRel->pr_desc;
					}
				],
				'ssph_txn_num', 
				[
					'attribute' => 'ssph_approved',
					'format' => 'raw',
					'value' => function($model){
						$i1 = $model->ssph_approved ? '<i class="fa fa-check" aria-hidden="true"></i>' : '-';
						$bg1 = $model->ssph_approved ? 'success' : 'warning';

						$bg2 = $model->ssph_registrarApproved ? 'success' : 'warning';
						$i2 = $model->ssph_registrarApproved ? '<i class="fa fa-check" aria-hidden="true"></i>' : '-';

						return "<span title='CEP Clerk approval status' class='bg-{$bg1}'>{$i1}</span> | <span title='Registrar Head approval status'  class='bg-{$bg2}'>{$i2}</span>";
					}
				],
		        
			],
		]);
		?>
    </div>
  </div>
<!-----End current batch fees details----->
