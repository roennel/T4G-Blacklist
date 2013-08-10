<?php

$alias = array
(
  'submission' => 'Submission',
  'appeal' => 'Appeal',
  'forum' => 'Forum',
  'general' => 'General'
);

?>
<div class="sub extended">
  
  <h2>User Voting</h2>
  
  <div style="margin-bottom: 10px">
    + <a href="addUserVote">Add Vote</a> | <a href="?type=list">List</a> | <a href="?type=ranking">Ranking</a>
  </div>
  
  <?php if(@$_GET['type'] == 'ranking'): ?>
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th>User</th>
        <th>Score</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data->ranking as $item): ?>
      <tr>
        <td><?=getUser($item->userId)->username ?></td>
        <td><?=$item->score ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  <?php else: ?>
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th>Date</th>
        <th>User</th>
        <th>Author</th>
        <th>Type</th>
        <th>Link</th>
        <th>Reason</th>
        <th>Score</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->items->fetch()): ?>
      <tr>
        <td><?php echo date('d.m.Y H:i', $item->date) ?></td>
        <td><?php echo getUser($item->userId)->username ?></td>
        <td><?php echo getUser($item->authorUserId)->username ?></td>
        <td><?php echo $alias[$item->type] ?></td>
        <td><a href="<?php echo $item->link ?>" target="_blank">link</a></td>
        <td><?php echo nl2br(urldecode(process($item->reason))) ?></td>
        <td><span class="<?= $item->score < 0 ? 'fail' : ($item->score > 0 ? 'success' : '') ?>"><?php echo $item->score < 0 ? $item->score : ('+' . $item->score); ?></span></td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  <?php endif ?>
  
</div>
