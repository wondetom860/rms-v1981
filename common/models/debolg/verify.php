<?php 
namespace common\models\debolg;
// read signature and public key file from the path:

use common\components\CommonControllerFunctions as CCF;

class verify{

    // public $_sign = file_get_contents("../../../DECRIS_7.sig");
    // public $pkeys = file_get_contents("../../../public.pem");
    protected $filenameE;
    public $lic = LIC_KEY;

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
        $handle = fopen('js/'.signature['accronym'].'-'.signature['version'].'.prof.json', 'wbx');
        fwrite($handle,$jsonfile);
        fclose($handle);

        return $this->verify($jsonfile);
    }
}

 ?>