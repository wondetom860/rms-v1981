<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\DepartmentSelection\models\DepartmentSelectEntrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Approve Department Select Entry');
// $this->params['breadcrumbs'][] = ['label' => "Main", 'url' => ['/DepartmentSelection']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-select-entry-index" style="font-size: 10pt;">

    <h3><?= Html::encode($this->title) ?>
    	<p class="pull-right">
    		<button class="btn btn-danger btn-xs" onclick="reportFormat(1);return false;">Report V1</button>
	    	<button class="btn btn-danger btn-xs" onclick="reportFormat(2);return false;">Report V2</button>
	    	<button class="btn btn-danger btn-xs" onclick="reportFormat(3);return false;">Report V3</button>
    	</p>
    </h3>

    <div class="row" style="background-color: #FFF;">
    	<div class="col-md-12">
    		<div class="alert alert-warning">
    			Choose a department for students. The actions are forward only, you can NOT undo the operation once you complete it.
    		</div>
    	</div>
    	<div class="col-md-3">
    		<?= $this->render('_includes/setting_entries',['model' => $selections]) ?>
    	</div>
        <div class="col-md-9 hidden" id="dept_choice_container" style="border: 1px solid black">
            
        </div>
        
    </div>

    <?php
        Modal::begin([
            'header'=>'<h4></h4>',
            'id'=>'modal',
            'size'=>'modal-md',
        ]);
        echo "<div id='modalContent' class='container-fluid'>Loading form...</div>";
        Modal::end();
    ?>


    <script type="text/javascript">
        approvedChoice  = {};
        settingID = '';

        window.showStudentInfoView = (studentId) =>{
            $('#modal').find('.modal-header').html('Student Profile Information');
            $('#modalContent').css('text-align','left');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $('#modalContent').html('Loading Student Information');
            $.get('/student/view?id='+studentId,{}).done(function(data){
                $('#modalContent').html(data);
            });
        }
        window.cancelSelection = (entryId)=>{
            if (confirm('Are you sure to save operation?')) {
                    $.post("/DepartmentSelection/department-select-entry/cancel-selection-action",{entryId:entryId}).done((resp) => {
                        if (resp == 1) {
                            loadSelectionEntry(settingID);
                        }else{
                            alert(resp);
                        }
                    });
                }
        }
        window.saveSelection = ()=>{
            // console.log();
            if (Object.keys(approvedChoice).length > 0) {//list is NOT empty
                if (confirm('Are you sure to save operation?')) {
                    $.post("/DepartmentSelection/department-select-entry/approve-selection-action",{approvedChoice:JSON.stringify(approvedChoice)}).done((resp) => {
                        if (resp == 1) {
                            loadSelectionEntry(settingID);
                        }else{
                            alert(resp);
                        }
                    });
                }
            }
        }
        window.setChoice = (studentId,deptId)=>{
            approvedChoice[studentId] = deptId;
            // console.log(JSON.stringify(approvedChoice));
        }

        window.showEducBkgrnd = (entryId) => {
            $('#modal').find('.modal-header').html('Student Education Background Information');
            $('#modalContent').css('text-align','left');
            $('#modal').find('.modal-dialog').addClass('modal-md');
            $('#modal').modal('show');
            $('#modalContent').html('Loading Education Background Data...');
            $.post('/DepartmentSelection/department-select-entry/educ-background-info',{entryId:entryId}).done(function(data){
                $('#modalContent').html(data);
            });
        }
        window.loadSelectionEntry = (settingId)=>{
            approvedChoice  = {};
            settingID = settingId;
            $("#dept_choice_container").html('Loading Selection Entry....').removeClass('hidden');
            $.post('/DepartmentSelection/department-select-entry/get-entries',{settingId:settingId}).done((resp)=>{
                $("#dept_choice_container").html(resp);
            });
        }
    </script>
</div>