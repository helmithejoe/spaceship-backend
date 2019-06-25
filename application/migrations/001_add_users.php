<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_users extends CI_Migration {
    
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'first_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'last_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'active_flag' => array(
                'type' => 'VARCHAR',
                'constraint' => '1',
                'default' => '0'
            )
        ));
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('users');
    }
    
    
}