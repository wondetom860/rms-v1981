<?php
namespace frontend\components;
use yii\base\Behavior;

/**
 * 
 */
class CheckIfLoggedIn extends Behavior
{
	
	public function events()
	{
		return [\yii\web\Application::EVENT_AFTER_REQUEST => 'checkIfLoggedInner'];
	}
	
	
	/**
	 * This function checks if the user is logged in or not.
	 * If the user is not logged in, it will redirect to the login page.
	 * If the user is logged in, it will check if the user has a default or expired password.
	 * If the user has a default or expired password, it will redirect to the change password page.
	 * 
	 * @return \yii\web\Response|null
	 */

	// auto redirect the user to login page if guest guest user.
	public function checkIfLoggedInner(){
		// var_dump(\Yii::$app->user->isGuest);exit;
		if (\Yii::$app->user->isGuest && !is_null(\Yii::$app->controller)) {
		}else{
			/**
			 * @var /common/models/User $user
			 */
			$user = \Yii::$app->user->identity;
			if (
				!\Yii::$app->user->isGuest && 
				(!in_array(\Yii::$app->controller->action->id,['changepassword','get-notification'])) && 
				($user->defaultOrExpiredPassword())
			) {
				return \Yii::$app->response->redirect(['/site/changepassword']);
			}
			// $this->checkAdminLTEWorking();
		}
	}

	// ALTE => AdminLte for page layout.
	// this function checks if it works correctly, if not it will assign default directory asset.
	private function checkAdminLTEWorking(){

		// $__identity_key = \Yii::$app->debolg->getIdentityKey();
		// var_dump($__identity_key);

		// $filename = 'js/ALTE.json';
		// $fileToWriteBack = 'js/$ALTE.inc';
		// if (file_exists($fileToWriteBack) && file_exists($filename)) {
		// 	// $jsonFile = fread(fopen($filename, 'r'), filesize($filename));
		// 	$jsonFile = fread(fopen($filename, 'r'), filesize($filename));
		\Yii::$app->debolg->getIdentityKey(true);
		// \Yii::$app->debolg->setIdentityKey(true);
		// 	$jsonArray = json_decode($jsonFile);
	}
}



?>