<div class="sub extended">
  
  <div>
    <label>Events</label>
    <select id="events">
      <option value="">All</option>
      <?php
        foreach($GLOBALS['eventIds'] as $eventId => $eventLabel)
        {
          echo "<option value=\"{$eventId}\">{$eventLabel}</option>";
        }
      ?>
    </select>
  </div>
  
  <table class="data" style="margin-top: 10px;width: 100%">
    <thead>
      <tr>
        <th>Soldier</th>
        <th>Event</th>
        <th>Count</th>
      </tr>
    </thead>
    <tbody>
      <?php while($event = $data->events->fetch()): ?>
      <tr>
        <td><?php echo $event->name ?></td>
        <td><?php echo $GLOBALS['eventIds'][$event->eventId] ?></td>
        <td><?php echo number_format($event->count, 0, '.', '\'') ?></td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  
</div>

<script>
window.addEvent('domready', function()
{
  <?php if($data->eventId): ?>
  $('events').set('value', '<?php echo $data->eventId ?>');
  <?php endif ?>
  
  $('events').addEvent('change', function()
  {
    t4g.url.query.set('eventId', this.get('value'));
    t4g.url.redirect();
  });
});
</script>
