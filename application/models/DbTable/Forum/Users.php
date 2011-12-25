<?php
class Model_DbTable_Forum_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'forum_users';
	
	public function updateEmail($id,$email)
	{
		$id = (int)$id;
		$data = array('user_email' => $email);
		$where['user_id = ?'] = $id;
		$this->update($data,$where);
	}
}

/*
 * ---------following code should be used when writing the save() function for this model-------------
 * global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
        define('IN_PHPBB', true);
        define('PHPBB_INSTALLED', true);
        $phpbb_root_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $phpbb_root_path = substr($phpbb_root_path, 0, strlen($phpbb_root_path) - 27);
        $phpbb_root_path = $phpbb_root_path . "public" . DIRECTORY_SEPARATOR . "forums" . DIRECTORY_SEPARATOR;
        include($phpbb_root_path . 'common.php');
        include($phpbb_root_path . 'includes/functions_user.php');

        user_add($forumData);
 */

/**
 * --------following code should be used when writing the deleteUser() function for this model----------
 * global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
        define('IN_PHPBB', true);
        define('PHPBB_INSTALLED', true);

        $phpbb_root_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $phpbb_root_path = substr($phpbb_root_path, 0, strlen($phpbb_root_path) - 27);
        $phpbb_root_path = $phpbb_root_path . "public" . DIRECTORY_SEPARATOR . "forums" . DIRECTORY_SEPARATOR;
        include($phpbb_root_path . 'common.php');
        include($phpbb_root_path . '/includes/functions_user.php');
        user_delete("remove", $this->userId);
 */
/* don't forget to write a setfullname function 
 * infact you've to write get and set for nearly 4 columns
 */
