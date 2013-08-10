<div class="alxDev alxAvailableEvents">
<?php foreach($this->eventCollection->getEvents() as $event): ?>	
	<div class="alxEvent">
		<?php var_dump($event) ?>
	</div>
<?php endforeach ?>
</div>