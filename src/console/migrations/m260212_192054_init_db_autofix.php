<?php

use yii\db\Migration;

class m260212_192054_init_db_autofix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%user_profile}}', [
            'id' => $this->primaryKey(),

//            "objectId": "String"
//            "createdAt": "Date"
//            "updatedAt": "Date"
//            "specialization": "String" id специализации
//            "fullName": "String"
//            "role": "String" мастер или водитель
//            "userId": "String"
//            "phone": "String"
//            "carBrand": "String" только у клиента
//            "photoUrl": "String"
//            "services": "Array"
//            "cars": "Array" хз зачем оно
//            "specializations": "Array"
//            "city": "String"
//            "email": "String"
//            "rating": "Number"
//            "reviewsCount": "Number"
//            "workShifts": "Array" список смен только к мастера
//            "latitude": "Number"
//            "longitude": "Number"
//            "workAddress": "String"
//            "photos": "Array"
//            "firstName": "String"
//            "lastName": "String"
//            "experience": "Number" стаж лет
//            "companyName": "String"
//            "customShiftTemplates": "Array"


//            "specialization": "String" id специализации
            'fullName' => $this->string()->notNull(),
            'role' => $this->tinyInteger(1)->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'phone' => $this->string()->notNull(),
            'carBrand' => $this->string()->notNull(), //только у клиента
//            "photoUrl": "String" //В бдругой базе будет
//            "services": "Array" // этого не будет
//            "cars": "Array" хз зачем оно
//            "specializations": // это тоже в отдельной таблице
            'city' => $this->string()->defaultValue(null),
            'email' => $this->string()->defaultValue(null),
            'rating' => $this->tinyInteger()->notNull()->defaultValue(0),
            'reviewsCount' => $this->integer()->notNull()->defaultValue(0),
//            "workShifts": "Array" список смен только к мастера // Это в отдельной таблице
            'latitude' => $this->integer()->defaultValue(null),
            'longitude' => $this->integer()->defaultValue(null),
            'workAddress' => $this->string()->defaultValue(null),
//            "photos": "Array" // тоже в отдельной таблице файлы
            'firstName' => $this->string()->defaultValue(null),
            'lastName' => $this->string()->defaultValue(null),
            'experience' => $this->tinyInteger()->unsigned()->defaultValue(0), //стаж лет
            'companyName' => $this->string()->defaultValue(null), //название организации, это только для мастера
//            "customShiftTemplates": "Array"

            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),


//            'text' => $this->text(),
//            'user_from' => $this->integer()->unsigned()->notNull(),
//            'user_to' => $this->integer()->unsigned()->notNull(),
//            'topic_id' => $this->integer()->unsigned()->notNull(),
//            'created_at' => $this->integer()->unsigned()->notNull(),
//            'updated_at' => $this->integer()->unsigned()->notNull(),
//            'is_read' => $this->smallInteger()->unsigned(),
        ]);

        $this->createTable('{{%booking}}', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->unsigned()->notNull(),
            'master_id' => $this->integer()->unsigned()->notNull(),

            'slotStart' => $this->timestamp()->notNull(),
            'slotEnd' => $this->timestamp()->notNull(),
            'serviceName' => $this->string()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
//            "useMasterKey": "Boolean" // хз зачем это



//            "objectId": "String"
//            "createdAt": "Date"
//            "updatedAt": "Date"
//            "ACL": "ACL"
//            "slotEnd": "Date"
//            "slotStart": "Date"
//            "master": "Pointer", "targetClass": "UserProfile"
//            "client": "Pointer", "targetClass": "UserProfile"
//            "serviceName": "String"
//            "status": "String"
//            "masterId": "String"
//            "clientId": "String"
//            "useMasterKey": "Boolean"

            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),


        ]);

        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),

//            "objectId": "String"
//            "createdAt": "Date"
//            "updatedAt": "Date"
//            "ACL": "ACL"
//            "promotionData": "Object"
//            "amount": "Number"
//            "paymentId": "String"
//            "confirmationUrl": "String"
//            "status": "String"
//            "paid": "Boolean"
//            "userId": "String"

            'promotion_id' => $this->integer()->unsigned()->notNull(),
            'userId' => $this->integer()->unsigned()->notNull(),
            'payment_id' => $this->integer()->unsigned()->notNull(),
            'amount' => $this->decimal(12, 2)->defaultValue(0),
            'confirmationUrl' => $this->string()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'paid' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),


        ]);

        $this->createTable('{{%promotion}}', [
            'id' => $this->primaryKey(),

//            "objectId": "String"
//            "createdAt": "Date"
//            "updatedAt": "Date"
//            "ACL": "ACL"
//            "title": "String", "required": false
//            "description": "String", "required": false
//            "validUntil": "String", "required": false
//            "order": "Number", "required": false
//            "phoneNumber": "String", "required": false
//            "conditions": "String", "required": false
//            "imageUrl": "String", "required": false
//            "createdByMasterId": "String"
//            "isPaid": "Boolean"
//            "publishedUntil": "Date"

            'title' => $this->string()->notNull(),
            'description' => $this->string()->notNull(),
            'validUntil' => $this->timestamp()->defaultValue(null)->comment('дата окончания самой акции (для клиента)'),
            'order' => $this->integer()->unsigned(),
            'phoneNumber' => $this->string()->defaultValue(null),
            'conditions' => $this->string()->defaultValue(null),
//            "imageUrl": "String", "required": false // фото будет храниться отдельно
            'master_id' => $this->integer()->unsigned(),
            'isPaid' => $this->boolean()->defaultValue(false),
            'publishedUntil' => $this->timestamp(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),


        ]);

        $this->createTable('{{%review}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'master_id' => $this->integer()->unsigned()->notNull(),
            'rating' => $this->tinyInteger()->notNull()->defaultValue(0),
            'text' => $this->text()->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),

//            "objectId": "String"
//            "createdAt": "Date"
//            "updatedAt": "Date"
//            "ACL": "ACL"
//            "masterId": "String"
//            "authorName": "String"
//            "rating": "Number"
//            "authorId": "String"
//            "text": "String"



        ]);

        $this->createTable('{{%specializations}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(0),
            'description' => $this->string(255)->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addCommentOnTable('{{%specializations}}', 'Мастер в своем профиле выбирает специализации по которым он работает (двигатель, трансмиссия, выхлоп). Специализация - это категория');


        $this->createTable('{{%specialization}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'specialization_id' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->addCommentOnTable('{{%specialization}}', 'Связь пользователя со спецализаций)');


        $this->createTable('{{%services}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'specialization_id' => $this->boolean()->notNull()->defaultValue(0),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255)->defaultValue(null),
            'price_from' => $this->decimal(12, 2)->defaultValue(0),
            'price_to' => $this->decimal(12, 2)->defaultValue(0),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addCommentOnTable('{{%services}}', 'Связь пользователя со спецализаций)');


        $this->createTable('{{%work_shifts}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),


            // :TODO доделать

            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addCommentOnTable('{{%work_shifts}}', 'Смены работы мастеров)');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%review}}');
        $this->dropTable('{{%user_profile}}');
        $this->dropTable('{{%promotion}}');
        $this->dropTable('{{%payment}}');
        $this->dropTable('{{%booking}}');
        $this->dropTable('{{%services}}');
        $this->dropTable('{{%specializations}}');
        $this->dropTable('{{%specialization}}');
    }

}
