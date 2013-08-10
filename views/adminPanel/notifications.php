<div class="sub extended">
  
  <h2>Notifications</h2>
  
  <?php foreach($data->groups as $groupName => $items): ?>
  <br />
  <h3><?= $groupName ?></h3>
  <table class="data" style="width: 100%; margin-bottom: 20px;">
    <thead>
      <tr>
        <th style="width: 120px">Date</th>
        <th>Type</th>
        <th>Author</th>
        <th>Link</th>
        <th>Reason</th>
        <th>Score</th>
        <th style="width: 100px">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $items->fetch()): ?>
      <?php
        $read = $item->read == '1' ? true : false;
      ?>
      <tr notificationId="<?= $item->userNotificationId ?>" unread="<?= $read ? 0 : 1 ?>" style="<?= $read ? "opacity: 0.8" : "" ?>">
        <td><?php echo date('d.m.Y H:i', $item->date) ?></td>
        <td>User Vote</td>
        <td><?php echo getUser($item->authorUserId)->username ?></td>
        <td><a href="<?php echo $item->link ?>" target="_blank">link</a></td>
        <td><?php echo nl2br(urldecode(process($item->reason))) ?></td>
        <td><span class="<?= $item->score < 0 ? 'fail' : ($item->score > 0 ? 'success' : '') ?>"><?php echo $item->score < 0 ? $item->score : ('+' . $item->score); ?></span></td>
        <td>
          <input type="button" class="toggleRead" value="<?= $read ? "Mark Unread" : "Mark Read" ?>" />
        </td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  <?php endforeach ?>
</div>

<div class="sub extended">

  <h2>Notifications Created by You</h2>
  <br />
  <table class="data" style="width: 100%;">
    <thead>
      <tr>
        <th style="width: 120px">Date</th>
        <th>Type</th>
        <th>Target</th>
        <th>Link</th>
        <th>Reason</th>
        <th>Score</th>
        <th style="width: 100px">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->myNotifications->fetch()): ?>
      <?php
        $read = $item->read == '1' ? true : false;
      ?>
      <tr notificationId="<?= $item->userNotificationId ?>" unread="<?= $read ? 0 : 1 ?>" style="<?= $read ? "opacity: 0.8" : "" ?>">
        <td><?php echo date('d.m.Y H:i', $item->date) ?></td>
        <td>User Vote</td>
        <td><?php echo getUser($item->userId)->username ?></td>
        <td><a href="<?php echo $item->link ?>" target="_blank">link</a></td>
        <td><?php echo nl2br(urldecode(process($item->reason))) ?></td>
        <td><span class="<?= $item->score < 0 ? 'fail' : ($item->score > 0 ? 'success' : '') ?>"><?php echo $item->score < 0 ? $item->score : ('+' . $item->score); ?></span></td>
        <td>
          <span class="<?= $read ? "success" : "fail" ?>"><?= $read ? "Read" : "Unread" ?></span>
        </td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">
  $$('.toggleRead').addEvent('click', function(e)
  {
    var row = this.getParent().getParent();
    var id = row.get('notificationId');
    var unread = row.get('unread') == '1';
    
    this.disabled = true;
    
    new Request.JSON
    ({
      url: 'markNotificationRead',
      onSuccess: function(response)
      {
        t4g.url.redirect(); // refresh
      }
    }).get({notificationId: id, userId: '<?= getUserId() ?>', setUnread: unread ? 0 : 1});
  });
</script>
