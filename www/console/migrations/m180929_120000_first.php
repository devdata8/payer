<?php

use yii\db\Migration;

class m180929_120000_first extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        // add table `user`
        $this->createTable('{{%user}}', [
            'id'                   => $this->primaryKey(),
            'username'             => $this->string()->notNull()->unique(),
            'auth_key'             => $this->string(32)->notNull(),
            'password_hash'        => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email'                => $this->string()->notNull()->unique(),
            'status'               => $this->smallInteger()->notNull()->defaultValue(10),
            'balance'              => $this->float()->notNull()->defaultValue(0),
            'deferred_balance'     => $this->float()->notNull()->defaultValue(0),
            'created_at'           => $this->integer()->notNull(),
            'updated_at'           => $this->integer()->notNull(),
        ], $tableOptions);
        // add table `payment`
        $this->createTable('{{%payment}}', [
            'id'            => $this->primaryKey(),
            'id_user_from'  => $this->integer()->notNull(),
            'id_user_to'    => $this->integer()->notNull(),
            'status'        => $this->integer()->notNull()->defaultValue(1),
            'amount'        => $this->float()->notNull(),
            'deferred_time' => $this->integer()->notNull(),
            'created_at'    => $this->integer()->notNull(),
            'updated_at'    => $this->integer()->notNull(),
        ], $tableOptions);
        // add foreign key for table `payment`
        $this->addForeignKey(
            'fk-payment-id_user_from',
            'payment',
            'id_user_from',
            'user',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-payment-id_user_to',
            'payment',
            'id_user_to',
            'user',
            'id',
            'CASCADE'
        );
        // insert data for table `user`
        $this->insert('{{%user}}', [
            'id'                   => 5,
            'username'             => 'ros',
            'balance'              => '900',
            'deferred_balance'     => '0',
            'auth_key'             => 'vCoX6JH1mfpu59AYo7UaDhhvNluC_9ue',
            'password_hash'        => '$2y$13$QlTz2hR6u03BprLXiX.Iz.1RH7UG28NlAYyrUveyDumU4P305XZNq',
            'password_reset_token' => null,
            'email'                => 'rrsrusakov@gmail.com',
            'status'               => 10,
            'created_at'           => time(),
            'updated_at'           => time(),
        ]);
        $this->insert('{{%user}}', [
            'id'                   => 6,
            'username'             => 'user1',
            'balance'              => '100',
            'deferred_balance'     => '0',
            'auth_key'             => 'OYrmOt4Gku8beqXNpJKjCD145cwXJEsz',
            'password_hash'        => '$2y$13$y7pjE.WcclVgfvj2IjByruFC7vUcRrsvClFwtOHdW/px16vRY53/W',
            'password_reset_token' => null,
            'email'                => 'user1@test.lo',
            'status'               => 10,
            'created_at'           => time(),
            'updated_at'           => time(),
        ]);
        $this->insert('{{%user}}', [
            'id'                   => 7,
            'username'             => 'user2',
            'balance'              => '0',
            'deferred_balance'     => '0',
            'auth_key'             => 'SHkmikvlz46XoD8GHhWuuxPswQO98NyS',
            'password_hash'        => '$2y$13$tHTHsD1xphiDe6jn2nu/Xek4.wo5uNwwgEGutiYgLrf6bWSTqo/86',
            'password_reset_token' => null,
            'email'                => 'user2@test.lo',
            'status'               => 10,
            'created_at'           => time(),
            'updated_at'           => time(),
        ]);
    }

    public function down()
    {
        // drops foreign key for table `payment`
        $this->dropForeignKey(
            'fk-payment-id_user_from',
            'payment'
        );
        $this->dropForeignKey(
            'fk-payment-id_user_to',
            'payment'
        );
        // drops table `payment`
        $this->dropTable('{{%payment}}');
        // drops table `user`
        $this->dropTable('{{%user}}');
    }
}
