<?php

namespace common\modules\HRMS\controllers;

use yii\web\Controller;
use common\modules\HRMS\models\RmsUserLink;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $gender
 * @property string $auth_key
 * @property string $user_type
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $nationality
 * @property integer $place_of_birth_address_relId
 * @property integer $place_of_residence_address_relId
 * @property string $dob
 * @property string $dob_ET
 * @property string $photo_num
 * @property string $created_at
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $created_by
 **/
/**
 * Default controller for the `HRMS` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetUpdate(){//import updates on employees information from HRMMS
    	// stores employee updated information from hrms to DECIS

    	$hrms_user_link_id = isset($_POST['rmsLinkEntryId']) && !empty($_POST['rmsLinkEntryId']) ? intval($_POST['rmsLinkEntryId']) : die('Invalid ReqUest');

    	$entry = RmsUserLink::findOne($hrms_user_link_id);

    	if ($entry && count($entry) > 0) {
    		$person = $entry->personRel;

    		$rmsUser = $entry->rmsAll();
    		// var_dump($rmsUser);die();

    		$rmsUser->first_name = $person->first_name;
    		$rmsUser->middle_name = $person->middle_name;
    		$rmsUser->last_name = $person->last_name;
    		$rmsUser->gender = $person->gender;
    		$rmsUser->user_type = $person->profession_type;
    		$rmsUser->dob = $person->dob;
    		$rmsUser->userRole = 'Student';//simply place holder for role, but not actually written to the DB
    		$rmsUser->email = $person->email;//empty($person->email) ? $rmsUser->email : 

    		if ($rmsUser->update()) {
    			return 1;
    		}
    		return json_encode($rmsUser->getErrors());
    	}else{
    		return "InvaliD Link...";
    	}

    }
}
