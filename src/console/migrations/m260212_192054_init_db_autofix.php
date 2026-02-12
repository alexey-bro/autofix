<?php

use yii\db\Migration;

class m260212_192054_init_db_autofix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        /*
           {
    "className": "UserProfile",
    "fields": {
      "objectId": {
        "type": "String"
      },
      "createdAt": {
        "type": "Date"
      },
      "updatedAt": {
        "type": "Date"
      },
      "ACL": {
        "type": "ACL"
      },
      "specialization": {
        "type": "String"
      },
      "fullName": {
        "type": "String"
      },
      "role": {
        "type": "String"
      },
      "userId": {
        "type": "String"
      },
      "phone": {
        "type": "String"
      },
      "carBrand": {
        "type": "String"
      },
      "photoUrl": {
        "type": "String"
      },
      "services": {
        "type": "Array"
      },
      "cars": {
        "type": "Array"
      },
      "specializations": {
        "type": "Array"
      },
      "city": {
        "type": "String"
      },
      "email": {
        "type": "String"
      },
      "rating": {
        "type": "Number"
      },
      "reviewsCount": {
        "type": "Number"
      },
      "workShifts": {
        "type": "Array"
      },
      "latitude": {
        "type": "Number"
      },
      "longitude": {
        "type": "Number"
      },
      "workAddress": {
        "type": "String"
      },
      "photos": {
        "type": "Array"
      },
      "firstName": {
        "type": "String"
      },
      "lastName": {
        "type": "String"
      },
      "experience": {
        "type": "Number"
      },
      "companyName": {
        "type": "String"
      },
      "customShiftTemplates": {
        "type": "Array"
      }
    },
         */

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260212_192054_init_db_autofix cannot be reverted.\n";

        return false;
    }
    */
}
