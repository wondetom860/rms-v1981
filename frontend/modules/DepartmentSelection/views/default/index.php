<?php 

$this->title = Yii::t('rms', 'Student Department Selection Main Entry');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="DepartmentSelection-default-index">
    <h3><?= $this->title ?></h3>
    <p>
        This Entry is for managing Students' department selection Entry, - Settings and Data Entries.
        <code class="pull-right"><?= "V1.0"?></code>
    </p><hr>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3>Setting Entries <button class="btn btn-primary btn-xs pull-right" onclick="manageSettings();return false;">Manage Setting</button></h3>
                </div>
                <div class="panel-body" style="font-size: 9pt;">
                    <?= $this->render('../selection-setting-entry/includes/__index-default') ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 hidden">
            <div class="panel panel-scondary">
                <div class="panel-heading">
                    <h3>Setting Entries <button class="btn btn-primary" onclick="manageSettings();return false;">Manage Selection</button> </h3>
                </div>
                <div class="panel-body">
                    
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    window.manageSettings = ()=>{
        window.location.href = "/DepartmentSelection/selection-setting-entry";
    }
</script>