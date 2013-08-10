<?php
/**
* @package application.php
* @copyright (c) JimA http://beta-garden.com 2009
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
require_once '/var/www/t4g_blacklist/lib/functions.php';
require_once '/var/www/t4g_blacklist/lib/phpbb_api.php';



/*
$sql = 'SELECT * FROM ' . USERS_TABLE . ' u WHERE user_id = ' . 133;
$result = $db->sql_query_limit($sql, 1);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$userdata = $row;
*/


$user->session_begin(false);
$user->session_create(1010, false, false, false);

/*
$fuser_id = 5;
$fuser_name = array();
$res = user_get_id_name($fuser_id, $fuser_name);
var_dump($fuser_name);
echo 'success\r\n';

echo group_user_add(13, 1010, false, false, true);
echo "\r\n";
echo group_user_del(13, 1010);
*/

var_dump($user);
echo "\r\n";
echo generate_board_url();
return;

// note that multibyte support is enabled here 
$my_subject = utf8_normalize_nfc('Important Information for Blacklist Moderators');
$my_text    = utf8_normalize_nfc('Congratulations and welcome to T4G Blacklist.

This is an automated message we send out to all new moderators for Blacklist.  It contains much of the information you will need to know to be an effective part of the staff here.  Please take the  time to review this information and ask questions often.  All of the staff here is willing to help and guide you as you get a feel for how we do things.
It is encouraged for you to install Skype as we use this as a Instant Messaging communication tool.  The link for that can be found below.  Once installed, contact Gazza at [url]http://forum.tools4games.com/ucp.php?i=pm&mode=compose&u=69[/url] to be added to the private T4G Blacklist group chat and T4G Moderators group chat.

As a member of the staff here you will be expected to maintain a minimum amount of activity working on submissions and appeals.  Activity is tracked and visible to all the staff just as you can view the activity of the other staff members.  This is a team effort and we want to share the workload since we are all here on a voluntary basis.

It is also very important that you visit this website often to participate in discussions or to simply keep yourself informed as we change any policies or procedures for dealing with different scenarios.  You are part of the team and your input will be valued so be sure to speak up if you have an opinion on anything.

These few links will take you to posts that are MANDATORY to read and understand.
[list][*][color=#FF8040]Policies and Procedures:[/color]  http://forum.tools4games.com/viewtopic.php?f=52&t=1519
[*][color=#FF8040]Non Disclosure Policy:[/color] http://forum.tools4games.com/viewtopic.php?f=54&t=457
[*][color=#FF8040]Moderator guide #1:[/color]  http://forum.tools4games.com/viewtopic.php?f=54&t=120
[*][color=#FF8040]Moderator guide #2:[/color]  http://forum.tools4games.com/viewtopic.php?f=54&t=1106
[*][color=#FF8040]SKYPE Download Link:[/color]  http://www.skype.com/en/download-skype/skype-for-computer/ [/list]

Some of the material found in those posts will be repetitive.  Ask questions when in doubt.  Remember that our job here is to ban "Cheaters, Stats-Padders and Glitchers" but that should NEVER be greater than our desire to not ban an innocent player.  Demand conclusive proof in your decision making and place detailed messages in your "Vote Message" to let others know WHY you voted the way you did and what evidence you used to come to your conclusion.

Good luck and thanks in advance for the time and effort you will be donating here.');


// variables to hold the parameters for submit_pm
$poll = $uid = $bitfield = $options = ''; 
generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

$data = array( 
    'address_list'      => array ('u' => array(133 => 'to')),
    'from_user_id'      => $user->data['user_id'],
    'from_username'     => $user->data['username'],
    'icon_id'           => 0,
    'from_user_ip'      => $user->data['user_ip'],
     
    'enable_bbcode'     => true,
    'enable_smilies'    => true,
    'enable_urls'       => true,
    'enable_sig'        => true,
            
    'message'           => $my_text,
    'bbcode_bitfield'   => $bitfield,
    'bbcode_uid'        => $uid,
);

echo 'Calling submit_pm';

echo submit_pm('post', $my_subject, $data, true);
?>