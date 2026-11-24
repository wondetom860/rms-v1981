<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "auth_org8n".
 *
 * @property int $id
 * @property string $name
 * @property string $abrvn
 * @property resource $logo
 * @property string|null $website
 * @property string|null $email
 * @property string $phone1
 * @property string|null $phone2
 * @property string|null $active_from
 * @property string|null $active_to
 * @property string $lic_entry
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $created_at
 * @property int $created_by
 * @property string $data
 */
class AuthOrg8n extends \yii\db\ActiveRecord
{

    public $logo_file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_org8n';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'abrvn', 'logo', 'phone1', 'lic_entry', 'created_at', 'created_by', 'data'], 'required'],
            [['name', 'logo', 'data'], 'string'],
            [['active_from', 'active_to', 'updated_at', 'created_at'], 'safe'],
            [['updated_by', 'created_by'], 'integer'],
            // ['logo_file', 'image', 'extensions' => 'jpg, png', 'maxSize' => 1024 * 1024 * 2],
            ['logo', 'file', 'extensions' => 'jpg,jpeg,png,gif', 'maxSize' => 1024 * 1024 * 2],
            [['abrvn', 'lic_entry'], 'string', 'max' => 100],
            [['website', 'email'], 'string', 'max' => 200],
            [['phone1', 'phone2'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'name' => Yii::t('rms', 'Name'),
            'abrvn' => Yii::t('rms', 'Abbrivation'),
            'logo' => Yii::t('rms', 'Logo'),
            'website' => Yii::t('rms', 'Website'),
            'email' => Yii::t('rms', 'Email'),
            'phone1' => Yii::t('rms', 'Phone1'),
            'phone2' => Yii::t('rms', 'Phone2'),
            'active_from' => Yii::t('rms', 'Active From'),
            'active_to' => Yii::t('rms', 'Active To'),
            'lic_entry' => Yii::t('rms', 'Lic Entry'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    protected function licensed(){
        return (Yii::$app->params['lic_key'] == $this->lic_entry) && $this->checkAltered();
    }

    public function getData(){
        $mainData = (object)[
            'name'=>$this->name,
            'lic_entry'=>$this->lic_entry,
            'abrvn'=>$this->abrvn,
            'logo'=>$this->logo,
            'website'=>$this->website,
            'email'=>$this->email,
            'phone1'=>$this->phone1,
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function getCollegeAddress(){
        return "Phone: ".$this->phone1." &nbsp;&nbsp;Email: ".$this->email." &nbsp;&nbsp; Website: ".$this->website."&nbsp;";
    }
}
