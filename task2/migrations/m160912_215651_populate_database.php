<?php

use yii\db\Migration;

class m160912_215651_populate_database extends Migration
{
    public function up()
    {
        $this->createTable('tbl_user', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'password' => 'string NOT NULL',
            'email' => 'string NOT NULL',
        ));
        echo 'Таблица tbl_user содана';
    }

    public function down()
    {
        $this->dropTable('tbl_user');
        echo 'Таблица tbl_user удалена';
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
