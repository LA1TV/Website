<?php

use uk\co\la1tv\website\commands\MediaItemEmailsSendLiveShortlyCommand;
use uk\co\la1tv\website\commands\MediaItemEmailsSendVodAvailableCommand;
use uk\co\la1tv\website\commands\DvrBridgeServiceSendPingsCommand;
use uk\co\la1tv\website\commands\DvrBridgeServiceRemoveDvrForVodCommand;
use uk\co\la1tv\website\commands\CreateSearchIndexCommand;
use uk\co\la1tv\website\commands\DeleteSearchIndexCommand;
use uk\co\la1tv\website\commands\SearchIndexCheckForItemsCommand;
use uk\co\la1tv\website\commands\UpdateSearchIndexCommand;
use uk\co\la1tv\website\commands\TriggerVODAvailableEventCommand;
use uk\co\la1tv\website\commands\CheckFileStoreAvailabilityCommand;

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new MediaItemEmailsSendLiveShortlyCommand());
Artisan::add(new MediaItemEmailsSendVodAvailableCommand());
Artisan::add(new DvrBridgeServiceSendPingsCommand());
Artisan::add(new DvrBridgeServiceRemoveDvrForVodCommand());
Artisan::add(new CreateSearchIndexCommand());
Artisan::add(new DeleteSearchIndexCommand());
// this should appear before UpdateSearchIndexCommand so that it will always run before it when scheduled to run at the same time
Artisan::add(new SearchIndexCheckForItemsCommand());
Artisan::add(new UpdateSearchIndexCommand());
Artisan::add(new TriggerVODAvailableEventCommand());
Artisan::add(new CheckFileStoreAvailabilityCommand());