<?php

# Create alxApplication Object
${ALX_APP_VAR} = new alxApplication;

# Search for Configs 
alxApplication::$systemLog->addLog
(
	'Searching for Configs', 
	${ALX_APP_VAR}->searchConfigs()
);

# Apply Correct Config
alxApplication::$systemLog->addLog
(
	'Applying Config', 			
	${ALX_APP_VAR}->applyConfig(@$overrideConfigId)
);

# Setting Application Stage Events
$events = array
(
	array('onApplicationInvoke'),
	array('onHeadersInvoke'),
	array('onDatabaseInvoke'),
	array('onSessionInvoke'),
	array('onRequestInvoke'),
	array('onRoutingInvoke'),
	array('onModulesInvoke'),
	array('onControllerInvoke'),
	array('onGlobalViewInvoke', true),
	array('onApplicationFinalize')
); 

# Create/Add Application Stage
$appStage = new alxApplicationStage;
$appStage->setId('application');
$appStage->setEventPath(alx::getToolkitPath() . 'alxApplication/events');
$appStage->setEvents($events);

alxApplication::$systemLog->addLog
(
	'Added Application Stage', 			
	${ALX_APP_VAR}->addApplicationStage($appStage)
);

# Create/Add Application Stage
$userStage = new alxApplicationStage;
$userStage->setId('user');
$userStage->setEventPath(alxApplication::getConfigVar('path', 'app') . '/events/application');
$userStage->setEvents($events);

alxApplication::$systemLog->addLog
(
	'Added Application Stage', 			
	${ALX_APP_VAR}->addApplicationStage($userStage)
);

# Load All Staged Events
alxApplication::$systemLog->addLog
(
	'Loading Staged Events', 			
	${ALX_APP_VAR}->loadApplicationStages()
);



# Run All Staged Events

alxApplication::$systemLog->addLog
(
	'Running Staged Events', 			
	${ALX_APP_VAR}->runApplicationStageEvents()
);
