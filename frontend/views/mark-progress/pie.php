<?php
use sjaakp\gcharts\PieChart;
?>
<p class="bg-success">Course Mark PieChart</p>
<?= PieChart::widget([
    'height' => '400px',
    'dataProvider' => $dataProvider,
    'columns' => [
        [
        	'attribute'=>'ca_std_relId',
        	'value'=>function($model){
        		return 'Corse Title';
        	}
        ],
        'ca_markObtained',
    ],
    'options' => [
        'title' => 'Mark By Course'
    ],
]) ?>

