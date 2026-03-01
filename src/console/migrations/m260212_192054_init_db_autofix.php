<?php

use yii\db\Migration;

class m260212_192054_init_db_autofix extends Migration
{



    public $specializationList = [
        'Шиномонтаж и колёса',
        'Техническое обслуживание (ТО)',
        'Подвеска и ходовая часть',
        'Тормозная система',
        'Выхлопная система',
        'Аварийные услуги',
        'Двигатель и система охлаждения',
        'Подвеска и ходовая часть',
        'Коробка передач и трансмиссия',
        'Климат и отопление',
        'Диагностика (общая)',
        'Тюниниг и модернизация',
        'Детейлинг и мойка',
        'Кузов и покраска',
        'Акумулятор и зарядка',
    ];

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
            'fullName' => $this->string()->defaultValue(null),
            'role' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'user_id' => $this->integer()->unsigned()->defaultValue(null),
            'phone' => $this->string()->notNull(),
            'carBrand' => $this->string()->defaultValue(null), //только у клиента
//            "photoUrl": "String" //В бдругой базе будет
//            "services": "Array" // этого не будет
//            "cars": "Array" хз зачем оно
//            "specializations": // это тоже в отдельной таблице
            'city' => $this->string()->defaultValue(null),
            'email' => $this->string()->defaultValue(null),
            'rating' => $this->tinyInteger()->notNull()->defaultValue(0),
            'reviewsCount' => $this->integer()->notNull()->defaultValue(0),
//            "workShifts": "Array" список смен только у мастера // Это в отдельной таблице
            'latitude' => $this->string()->defaultValue(null),
            'longitude' => $this->string()->defaultValue(null),
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

//            'slotStart' => $this->timestamp()->notNull(),
//            'slotEnd' => $this->timestamp()->notNull(),
            'work_time_shift_id' => $this->integer()->unsigned()->notNull(),
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

        $this->createIndex('idx-booking-client_id', '{{%booking}}', 'client_id');
        $this->createIndex('idx-booking-master_id', '{{%booking}}', 'master_id');
        $this->createIndex('idx-booking-work_time_shift_id', '{{%booking}}', 'work_time_shift_id');

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
            'user_id' => $this->integer()->unsigned()->notNull(),
            'payment_id' => $this->integer()->unsigned()->notNull(),
            'amount' => $this->decimal(12, 2)->defaultValue(0),
            'confirmationUrl' => $this->string()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'paid' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-payment-user_id', '{{%payment}}', 'user_id');
        $this->createIndex('idx-payment-payment_id', '{{%payment}}', 'payment_id');
        $this->createIndex('idx-payment-promotion_id', '{{%payment}}', 'promotion_id');

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

        $this->createIndex('idx-promotion-master_id', '{{%promotion}}', 'master_id');

        $this->createTable('{{%review}}', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->unsigned()->notNull(),
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

        $this->createIndex('idx-review-client_id', '{{%review}}', 'client_id');
        $this->createIndex('idx-review-master_id', '{{%review}}', 'master_id');

        $this->createTable('{{%specializations}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(0),
            'description' => $this->string(255)->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addCommentOnTable('{{%specializations}}', 'Мастер в своем профиле выбирает специализации по которым он работает (двигатель, трансмиссия, выхлоп). Специализация - это категория');

        foreach ($this->specializationList as $specialization) {

            Yii::$app->db->createCommand()
                ->insert('specializations', [
                    'name' => $specialization,
                ])->execute();

        }


        $this->createTable('{{%specialization}}', [
            'id' => $this->primaryKey(),
            'user_profile_id' => $this->integer()->notNull(),
            'specialization_id' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->addCommentOnTable('{{%specialization}}', 'Связь пользователя со спецализаций)');
        $this->createIndex('idx-specialization-user_profile_id', '{{%specialization}}', 'user_profile_id');
        $this->createIndex('idx-specialization-specialization_id', '{{%specialization}}', 'specialization_id');


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

        $this->addCommentOnTable('{{%services}}', 'Услуги, которые оказывает мастер)');
        $this->createIndex('idx-services-user_id', '{{%services}}', 'user_id');
        $this->createIndex('idx-services-specialization_id', '{{%services}}', 'specialization_id');


        $this->createTable('{{%work_days_shift}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'day' => $this->date()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addCommentOnTable('{{%work_days_shift}}', 'Рабочие дни смен)');
        $this->createIndex('idx-work_days_shift-user_id', '{{%work_days_shift}}', 'user_id');

        $this->createTable('{{%work_time_shift}}', [
            'id' => $this->primaryKey(),
            'work_days_shift_id' => $this->integer()->notNull(),
            'start' => $this->time(),
            'stop' => $this->time(),
            'isAvailable' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addCommentOnTable('{{%work_time_shift}}', 'Рабочие время в днях смен)');
        $this->createIndex('idx-work_time_shift-work_days_shift_id', '{{%work_time_shift}}', 'work_days_shift_id');


        $this->createTable('{{%custom_shift_templates}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'template' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

//        $this->addCommentOnTable('{{%work_time_shift}}', '');
        $this->createIndex('idx-custom_shift_templates-user_id', '{{%custom_shift_templates}}', 'user_id');

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

        $this->dropTable('{{%work_days_shift}}');
        $this->dropTable('{{%work_time_shift}}');
        $this->dropTable('{{%custom_shift_templates}}');
    }

}
