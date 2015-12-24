<?php
Event::listen('apiWebhookTest', 'uk\co\la1tv\website\api\eventHandlers\TestEventHandler@handle');
Event::listen('mediaItemLiveStream.live', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onLive');
Event::listen('mediaItemLiveStream.showOver', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onShowOver');
Event::listen('mediaItemLiveStream.notLive', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onNotLive');
Event::listen('mediaItemVideo.available', 'uk\co\la1tv\website\api\eventHandlers\MediaItemLiveHandler@onVodAvailable');