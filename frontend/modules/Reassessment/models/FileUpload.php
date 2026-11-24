<?php
   namespace frontend\modules\Reassessment\models;
   use yii\base\Model;
   class FileUpload extends Model {
      public $image,$fileName,$binaryfile,$pdf_file;
      public function rules() {
         return [
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, png'],
            [['pdf_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf, PDF'],
         ];
      }
      public function upload() {
         if ($this->validate()) {
            $this->image->saveAs('../uploads/' . $this->image->baseName . '.' .
               $this->image->extension);
            return true;
         } else {
            return false;
         }
      }
   }
?>