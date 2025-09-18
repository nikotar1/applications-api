<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%loan_requests}}`.
 */
class m250918_135223_create_loan_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%loan_requests}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->float(2)->notNull(),
            'term' => $this->integer()->notNull(),
            'status' => $this->string()->notNull(),
        ]);

        $this->createIndex('idx-loan_requests-user_id', '{{%loan_requests}}', 'user_id');

        // will prevent on db level to have 2 same user ids with approved status
        $this->execute("
          CREATE UNIQUE INDEX ux_approved_per_user
          ON loan_requests (user_id)
          WHERE status = 'approved';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%loan_requests}}');
    }
}
