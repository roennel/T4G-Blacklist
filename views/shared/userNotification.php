<?php
  $userId = getUserId();
  $notifications = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM user_notifications WHERE userId = '{$userId}' AND `read` = '0'")->fetch();
?>
<?php if($notifications->c > 0): ?>
  <div class="sub extended" style="text-align: center">
    You have <a href="/en/adminPanel/notifications"><?= $notifications->c ?> unread notification<?= $notifications->c > 1 ? 's' : '' ?></a>.
  </div>
<?php endif; ?>
