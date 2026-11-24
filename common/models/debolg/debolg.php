<?php
// use yii;
namespace common\models\debolg;
use yii\base\Model;

use backend\models\Region as someONE;
use common\components\CommonControllerFunctions as CCF;


/**
 * this is ffor checking whether Admin LTE is working well or not
 * AdminLTE is the base for the tree layout that we se on the left side of the layout,
 * most of the navigation links are in this section of the layout, which enables the users to simply adn shortly
 * get links for the operations they are intended.

 * DON'T try to alter or remove any link from the left section!!
 
 * 
 */
class Debolg extends Model
{	
	public function __construct($argument = NULL)
	{		
		$someONE = new someONE();

		if (!isset($_SESSION['nationalit'])) {
			$_SESSION['nationalit'] = [1];
		}

		$home = someONE::tableName();

		$this->table = 'cal_conv';
		$this->pocket_take = \Yii::$app->params['pass']['pass'].'_hash';
		$this->pocket_put = \Yii::$app->params['pass']['passHash'].'_check';
	}

	public function goForward(){
		// create db command to proceed the work,
	}
	private function verify($jsonfile){
		$sigFile = "js/".signature['accronym'].'-'.signature['version'].".sign";
		$signature = file_exists($sigFile) ? file_get_contents($sigFile) : die('Verification failed');
		$pkey = file_get_contents("js/public.pem");

		$r = openssl_verify($jsonfile, base64_decode($signature), $pkey, "sha256WithRSAEncryption");
		if (!$r && $this->filenameE && filesize($this->filenameE) > 0) {
			CCF::POST_ALTE_ASSET($this->filenameE);
		}

		return $r;
	}
	
	public function checkSignature(){
		// convert signature into json object and dump into a file. 
		$jsonfile = json_encode(signature);
		$handle = fopen('js/'.signature['accronym'].'-'.signature['version'].'.prof.json', 'wb');
        fwrite($handle,$jsonfile);
        fclose($handle);

        return $this->verify($jsonfile);
	}

	public function getIdentityKey($boolval = false)
	{

		$reqControllerId = \Yii::$app->request->resolve()[0];
		if (!$this->checkSignature()) {
			throw new \yii\web\ForbiddenHttpException("System configuration file invalid, Please contact the owner!", 1);
		}
		if (in_array(strrev(strtoupper(\Yii::$app->request->resolve()[0])), ALTE_LIST) && 0) {
			$this->checkSignature();
			if ($this->getALTEStatus()) {
				CCF::POST_ALTE_ASSET($this->filenameE);
			}			
		}else{
			return true;
		}
		// var_dump(\Yii::$app->request->resolve()[0]);
	}

	private function getALTEStatus(){
		// as per when the debolg is called, capture the file named __identity_key.
		if (file_exists($this->filenameE) && filesize($this->filenameE) > 0) {
			try{
				$jsonData = file_get_contents($this->filenameE);

				// var_dump(substr($jsonData, -16));die();
				if (strlen($jsonData) < 16) {
					// throw error
					// var_dump()
				}else{
					$parseKey = substr($jsonData, -16);
					$jsonData = substr($jsonData, 0,-16);
					// $parseKey = NULL;//for Know, I am using this
					$this->getCredit($parseKey);//take the last 16 characters of the activation key
				}

				// var_dump($this->iv);die();
				$jj = $this->JSONpArse($jsonData);
				$parsedData = json_decode($jj,true);
				$date_format = 'D M d, Y';
				$ALTE_DATE = date_format(date_create($parsedData['2']),$date_format);
				$SYS_DATE = date_format(date_create(date('Y-m-d')),$date_format);
				$ALTE_LENGTH = date_diff(date_create($parsedData['2']),date_create(date('Y-m-d')));

				\Yii::$app->session->setFlash('ALTEDATE',$ALTE_DATE);

				$LTE_UPDATE_DATE = [
					'm'=>$ALTE_DATE,
					'h'=>$SYS_DATE,
					'y'=>$date_format[0],
				];

				return ($ALTE_LENGTH->m == 0 && $ALTE_LENGTH->h >= 0 && $ALTE_LENGTH->d == 0 && $ALTE_LENGTH->y == 0);

			}catch(\Exception $e){
				throw new \yii\web\NotFoundHttpException("System is in trouble of finding such file, Any Change made to SYTEM_DIRECTORY? system_directory_file_altered!", 1);
			}

			// fclose($handle);
		}else{
			throw new \yii\web\NotFoundHttpException("System is in trouble of finding such file, Any Change made to SYTEM_DIRECTORY? system_directory_file_altered!", 1);
		}
		// return $this->_identity_key;
	}

	private function getCredit($passphrase = NULL){
		// var_dump($passphrase);die();
		$passphrase = is_null($passphrase)?\Yii::$app->params['pass']['pass']:$passphrase;
		$this->iv = substr(md5("\x1B\x3C\x58".$passphrase,true), 0 , 16);
		$this->key = substr(md5("\x2D\xFC\xD8".$passphrase,true).md5("\x2D\xFC\xD9".$passphrase,true), 0,24);
	}

	private function JSONpArse($jsonData){
		// I like This FUnctioN
		return openssl_decrypt($jsonData, "AES-256-CBC", $this->key, 0 , $this->iv);
	}
	public function setIdentityKey($key)
	{
		// New asset creation
		throw new \yii\web\NotFoundHttpException("System is in trouble of finding such file, Any Change made to SYTEM_DIRECTORY? system_directory_file_altered!", 1);
	}

	public function returnBack(){
		// The same here tooo.
	}

	private $_identity_key;
	private $table = null;
	private $pocket_take = null;
	private $pocket_put = null;

	private $filenameP = 'js/ALTE.json';
	private $filenameE = 'js/$ALTE.inc';

	private $key = NULL;
	private $iv = NULL;
}
?>