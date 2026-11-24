<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%logins}}".
 *
 * @property int $id
 * @property int $user_relId
 * @property string $ip_address
 * @property int $login_time
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $userRel
 */
class Logins extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%logins}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_relId', 'ip_address', 'login_time', 'created_at', 'created_by', 'data'], 'required'],
            [['user_relId', 'login_time', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'string'],
            [['ip_address'], 'string', 'max' => 45],
            [['user_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('hrms', 'ID'),
            'user_relId' => Yii::t('hrms', 'User Rel ID'),
            'ip_address' => Yii::t('hrms', 'Ip Address'),
            'login_time' => Yii::t('hrms', 'Login Time'),
            'created_at' => Yii::t('hrms', 'Created At'),
            'created_by' => Yii::t('hrms', 'Created By'),
            'updated_at' => Yii::t('hrms', 'Updated At'),
            'updated_by' => Yii::t('hrms', 'Updated By'),
            'data' => Yii::t('hrms', 'Data'),
        ];
    }

    public function alreadyImported()
    {
        return Logins::find()
            ->where([
                'user_relId' => $this->user_relId,
                'ip_address' => $this->ip_address,
                'login_time' => $this->login_time,
            ])
            ->count() > 0;
    }

    private function importFromLine($text)
    {
        if (isset($text[1]) && $text[1] != 'I') { //import only logins
            return false;
        }

        $text_to_array = explode(' has successfully ', $text);

        if (count($text_to_array) > 1) {
            $model = new Logins();
            $id = explode('=', $text_to_array[0]);
            $id = $id[1] ?? NULL;
            // $arr2 = explode(' from machine with IP address: ', $text_to_array[1]);
            $time = "";
            $start_delimiter = " the system on";
            $end_delimiter = " from machine ";
            $tt = $text_to_array[1];
            $start_pos = strpos($tt, $start_delimiter) + strlen($start_delimiter);
            $end_pos = strpos($tt, $end_delimiter);

            if ($start_pos !== false && $end_pos !== false && $end_pos > $start_pos) {
                $time = substr($tt, $start_pos, $end_pos - $start_pos);
                // echo $phrase; // Output: fox 
            }
            $date_string = str_replace(['[', ']', '@'], '', $time);

            // echo "<hr>";
            // var_dump($date_string, strtotime($date_string),time());
            // return false;


            $ip = substr($tt, $end_pos + strlen($end_delimiter . 'with IP address: '));


            $model->user_relId = trim(str_replace(['[', ']'], '', $id));
            $model->ip_address = trim($ip);
            $model->login_time = strtotime($date_string);

            if ($model->alreadyImported()) {
                return true;
            }

            $model->created_at = time();
            $model->created_by = Yii::$app->user->identity->id;

            $model->getData();
            return $model->save();
        }
        return false;
    }

    public function top10LoginSummary()
    {
        $this->importFromFile();
        $sql = "SELECT
                    u.username,
                    count(l.id) as login_count,
                    u.id as user_person_relId
                FROM
                    user  as u
                    JOIN logins as l on l.user_relId=u.id
                GROUP BY
                    u.username
                ORDER BY
                    login_count DESC, u.username ASC
                LIMIT 20";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        return $result;
    }

    public function importFromFile()
    {
        $file = 'uploads/login-log.wt';
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $key => $value) {
            if ($this->importFromLine($value)) {
                unset($lines[$key]);
            }
        }
        $newData = implode(PHP_EOL, array_values($lines)); // array_values re-indexes the array
        file_put_contents($file, $newData);
        return 1;
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'user_relId' => $this->user_relId,
            'ip_address' => $this->ip_address,
            'login_time' => $this->login_time,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]));
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[UserRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserRel()
    {
        return $this->hasOne(User::class, ['id' => 'user_relId']);
    }
}
