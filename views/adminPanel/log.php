<?php

$alias = array
(
  'setSubmissionState_Valid' => 'Voted on a Submission',
  'setSubmissionState_Invalid' => 'Voted on a Submission',
  'setSubmissionState_Revoked' => 'Revoked a Delay',
  'setSubmissionState_Delay' => 'Requested a Delay',
  'setSubmissionType_ch' => 'Changed Type to Cheating',
  'setSubmissionType_sp' => 'Changed Type to Statspadding',
  'setSubmissionType_gl' => 'Changed Type to Glitching',
  'setFinalSubmissionState_Valid' => 'Issued Ban to Blacklist',
  'setFinalSubmissionState_Invalid' => 'Negative Result, No Ban',
  'processImportedAppeal' => 'Processed Imported Ban Appeal',
  'deletedBanFromImportedAppeal' => 'Deleted Ban from Imported Ban Appeal',
  'setAppealState_Valid' => 'Voted on an Appeal',
  'setAppealState_Invalid' => 'Voted on an Appeal',
  'setFinalAppealState_Valid' => 'Removed Ban to Blacklist',
  'setFinalAppealState_Invalid' => 'Negative Appeal Result, Ban Stays',
  'setBanId_1' => 'Changed Ban to Cheating',
  'setBanId_2' => 'Changed Ban to Statspadding',
  'setBanId_3' => 'Changed Ban to Glitching',
  'markedSubmissionInvalid' => 'Marked Submission Invalid',
  'setUserAs_mod' => 'Added Moderator',
  'setUserAs_user' => 'Removed Moderator/Admin',
  'addNucleusId' => 'Added BF Profile',
  'setClanId' => 'Set Clan',
  'setCountry' => 'Set Country',
  'updatedPassword' => 'Updated Password',
  'requestPasswordReset' => 'Requested a Password Reset Mail',
  'resetPassword' => 'Reset Password',
  'verifiedEmail' => 'Verified Email Address'
);

$tags = alxDatabaseManager::fetchMultiple("SELECT * FROM tags");

foreach($tags as $tag)
{
  $alias["updateSubmissionTag_{$tag->tagId}"] = "Updated Tag State: {$tag->label}";
}

$type = $data->type;

$s = function($v, $r) use($type)
{
  return ($r == 'link') ? "/en/{$type}Panel/submissionDetail?submissionId={$v}" : $v;
};

$a = function($v, $r) use($type)
{
  // return "/en/{$type}Panel/appealDetail?appealId={$v}";
  return ($r == 'link') ? "/en/modPanel/appealDetail?appealId={$v}" : $v;
};

$b = function($v, $r) use($type)
{
  return ($r == 'link') ? "#" : $v;
};

$u = function($v, $r) use($type)
{
  return ($r == 'link') ? "/en/adminPanel/editUser?q={$v}" : getUser($v)->username;
};


$cb = array
(
  'setSubmissionState_Valid' => $s,
  'setSubmissionState_Invalid' => $s,
  'setSubmissionState_Delay' => $s,
  'setSubmissionState_Revoked' => $s,
  'setSubmissionType_ch' => $s,
  'setSubmissionType_sp' => $s,
  'setSubmissionType_gl' => $s,
  'setFinalSubmissionState_Valid' => $s,
  'setFinalSubmissionState_Invalid' => $s,
  'processImportedAppeal' => $a,
  'deletedBanFromImportedAppeal' => $a,
  'setAppealState_Valid' => $a,
  'setAppealState_Invalid' => $a,
  'setFinalAppealState_Valid' => $a,
  'setFinalAppealState_Invalid' => $a,
  'setBanId_1' => $b,
  'setBanId_2' => $b,
  'setBanId_3' => $b,
  'markedSubmissionInvalid' => $s,
  'setUserAs_mod' => $u,
  'setUserAs_user' => $u,
  'addNucleusId' => function($v, $r){ return $r == 'link' ? "http://battlefield.play4free.com/en/profile/{$v}" : $v; },
  'setClanId' => function($v, $r){ return $r == 'link' ? '#' : getClanName($v); },
  'setCountry' => function($v, $r){ return ($r == 'link' or $v == '') ? '' : $GLOBALS['countries'][$v]; },
  // 'updatedPassword' => ,
  'requestPasswordReset' => $u,
  'resetPassword' => $u,
  'verifiedEmail' => $u
);

foreach($tags as $tag)
{
  $cb["updateSubmissionTag_{$tag->tagId}"] = $s;
}
?>
<div class="sub extended">
  
  <h2><?php echo ucFirst($type) ?> Log</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th>Date</th>
        <th>User</th>
        <th>Action</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->log->fetch()): ?>
      <tr>
        <td style="white-space: nowrap;"><?php echo date('d.m.Y H:i:s', $item->date) ?></td>
        <td><?php echo $item->userId == '0' ? 'SYSTEM' : getUser($item->userId)->username ?></td>
        <td><?php echo $alias[$item->action] ?></td>
        <td>
          <?php if(isset($cb[$item->action])): ?>
          <a href="<?php echo $cb[$item->action]($item->value, 'link') ?>"><?php echo $cb[$item->action]($item->value, 'text') ?></a>
          <?php else:
            echo $item->value;
          endif ?>
        </td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>
