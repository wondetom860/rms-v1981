<?php

use yii\helpers\Html;
// use yii\web\UrlManager;
// use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this 
 * @var  backend\models\Instructor $model
 */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Instructors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructor-view">

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

    <div class="contianer-fluid">
        <div class="row">
            <div class="col-md-3">
                <center>
                    <div class="contianer">

                        <!-- <img height="200" width="110" class="img-circle" src="/uploads/{$model->instUserRel->photo_num}"> -->
                        <img height="200" width="180" class="img-circle" src="/uploads/<?= $model->instUserRel->photo_num ?>">

                    </div>
                    <div class="contianer bg-success">
                        <?= $model->getFullName() ?><br>
                        <small><?= $model->instUserRel->email ?></small>
                    </div>
                </center>
                <hr>
                <div class="container-fluid">
                    <h4>Profile From Human resourse Data Base</h4>
                    <?php if (isset($model->instUserRel->hrms_registration_code)): ?>
                        <div class="row">
                            <div class="hidden">
                                <input type="text" id="hrms_regsitration_code" value="<?= $model->instUserRel->hrms_registration_code ?>">
                            </div>
                            <div class="col-md-12">
                                <h3>Registration Code: <?= $model->instUserRel->hrms_registration_code ?></h3>
                            </div>
                            <div class="col-md-12" id="hrms_profile_container">

                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row" id="hrms_link_form_container">
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="hrms_reg_code" placeholder="Enter HRMS registration Code:">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-xs btn-primary" onclick="readCode();return false;">
                                    Link
                                </button>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <div class="col-md-9" style="vertical-align: middle;">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        // 'instDepRel.dept_name',
                        [
                            'attribute' => 'inst_dep_relId',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // $btnUpda
                                return $model->instDepRel->dept_name;
                            }
                        ],
                        // 'instAcqRel.acq_name',
                        // 'instAcrRel.acr_name',
                        // [],
                        [
                            'attribute' => 'User Type',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // $btnUpda
                                return $model->instUserRel->user_type;
                            }
                        ],
                        [
                            'attribute' => 'inst_acr_relId',
                            // 'format' => 'raw',
                            'value' => function ($model) {
                                // $btnUpda
                                return $model->instAcrRel->acr_name . ' (' . $model->instAcqRel->acq_name . ')';
                            }
                        ],
                        [
                            'attribute' => 'employment_type_relId',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // $btnUpda
                                return $model->employmentTypeRel ? $model->employmentTypeRel->et_name : '-';
                            }
                        ],
                        [
                            'attribute' => 'inst_active',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // $btnUpda
                                return $model->inst_active == 1 ? 'ACTIVE' : 'NOT ACTIVE';
                            }
                        ],
                        [
                            'attribute' => 'Contact Address',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $optionPhone = is_null($model->instUserRel->phone_mobile) ? 'Add' : 'Change';
                                $optionEmail = is_null($model->instUserRel->email) ? 'Add' : 'Change';
                                $addPhone = "<button class='btn btn-xs btn-info pull-right' onclick='manageUserPhone($model->inst_user_relId)'>{$optionPhone}</button>";
                                $addEmail = "<button class='btn btn-xs btn-info pull-right' onclick='manageUserEmail($model->inst_user_relId)'>{$optionEmail}</button>";
                                return "
                                    <p>Phone: <u>" . $model->instUserRel->phone_mobile . "</u>{$addPhone}</p>
                                    <p>Email: <u>" . $model->instUserRel->email . "</u>{$addEmail}</p>";
                            }
                        ],
                        [
                            'attribute' => 'Course Offerings',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $this->render('__reports/_course_offering', ['model' => $model]);
                            }
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>

</div>

<script>
    const fetchHrmsProfile = () => {
        code = reg_code;
        $.ajax({
            type: "get",
            dataType: 'jsonp',
            // params: jsonData,
            jsonp: false,
            // jsonpCallback: "callbackfn",
            url: 'http://<?= HRMS_URL . '/' . HRMS_VERSION . '/person/index' ?>',
            data: {
                id: code,
            },
            success: function(jqXHR, textStatus, errorThrown) {
                if (status == 200) {
                    alert("sucess function");
                } else {
                    alert(errorThrown);
                }
            },
            error: function(jqXHR, textStatus, errorThrown, data) {
                console.log('jqXHR: ' + jqXHR);
                alert(textStatus + " and<br> " + errorThrown);
            }
        }).then((resp) => {
            console.log('gjdhjghdjhghdhf');
            callbackfn(resp);
        });

        // function callbackfn(data) {
        //     alert(data);
        // }
    }

    reg_code = '';
    // const fetchHrmsProfile = () => {
    //     code = reg_code;
    //     $.ajax({
    //         url: 'http://<?= HRMS_URL . '/' . HRMS_VERSION . '/person/index' ?>',
    //         dataType: 'jsonp',
    //         type: "GET",
    //         cors: true,
    //         jsonpCallback: null,
    //         headers: {
    //             contentType: 'application/json',
    //             authorization: "Bearer admin",
    //             'Access-Control-Allow-Origin': "*",
    //         },
    //         // async: true,
    //         // crossDomain: true,
    //         data: {
    //             id: code,
    //         },
    //         success: function(response) {
    //             // alert('User information found in HRMS');
    //             alert(response);
    //             // // location.reload();
    //             callbackfn(response);
    //         },
    //         error: function(xhr, status, error) {
    //             // alert('Error: ' + error);
    //             console.log(xhr, status, error);
    //         }
    //     });
    // }

    window.callbackfn = function(data) {
        alert(data);
    };

    const readCode = () => {
        code = $("#hrms_reg_code").val();
        if (code == undefined || code == '') {
            code_input = $("#hrms_reg_code");
            code = code_input.value;
            if (code !== undefined || code !== '') {
                reg_code = code;
                fetchHrmsProfile();
            } else {
                code_input.focus();
            }
        } else {
            reg_code = code;
            fetchHrmsProfile();
        }
    }
    window.onload = function() {
        reg_code = $("#hrms_regsitration_code").val();

        if (reg_code == undefined || reg_code == '') {

        } else {
            fetchHrmsProfile;
        }
    }
    // fetchHrmsProfile();
    const manageUserEmail = (userId) => {
        email = prompt('Enter Address');
        if (email) {
            $.ajax({
                url: '/user/update-email',
                type: 'POST',
                data: {
                    userId: userId,
                    email: email
                },
                success: function(response) {
                    alert('Email number updated successfully');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                    console.log(xhr, status, error);
                }
            });
        }
    }
    const manageUserPhone = (userId) => {
        phone = prompt('Enter phone number');
        if (phone) {
            $.ajax({
                url: '/user/update-phone',
                type: 'POST',
                data: {
                    userId: userId,
                    phone: phone
                },
                success: function(response) {
                    alert('Phone number updated successfully');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                    console.log(xhr, status, error);
                }
            });
        }
    }
</script>