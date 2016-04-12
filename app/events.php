<?php
Event::listen('mediaItemLiveStream.live', 'uk\co\la1tv\website\notifications\MediaItemLiveHandler@onLive');
Event::listen('mediaItemLiveStream.showOver', 'uk\co\la1tv\website\notifications\MediaItemLiveHandler@onShowOver');
Event::listen('mediaItemLiveStream.notLive', 'uk\co\la1tv\website\notifications\MediaItemLiveHandler@onNotLive');
Event::listen('mediaItemVideo.available', 'uk\co\la1tv\website\notifications\MediaItemLiveHandler@onVodAvailable');

Event::listen('apiWebhookTest', 'uk\co\la1tv\website\api\eventHandlers\TestEventHandler@handle');
Event::listen('degradedService.stateChanged', 'uk\co\la1tv\website\api\eventHandlers\DegradedServiceEventHandler@onStateChanged');
Event::listen('mediaItemLiveStream.live', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onLive');
Event::listen('mediaItemLiveStream.showOver', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onShowOver');
Event::listen('mediaItemLiveStream.notLive', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onNotLive');
Event::listen('mediaItemVideo.available', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onVodAvailable');