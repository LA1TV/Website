<?php namespace uk\co\la1tv\website\models;

use Carbon;
use Config;
use Queue;
use Event;
use DB;
use Cache;
use Exception;
use uk\co\la1tv\website\models\DvrLiveStreamUri;

class MediaItemLiveStream extends MyEloquent {

	protected $table = 'media_items_live_stream';
	protected $fillable = array('enabled', 'state_id', 'information_msg', 'being_recorded', 'external_stream_url', 'end_time');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			// transaction committed in saved event
			// transaction important because entries created/removed in dvr_live_stream_uris
			// depending on stream state so must always change in sync with stream state.
			DB::beginTransaction();
			
			if ($model->hasJustLeftLive() && $model->hasJustBecomeStreamOver()) {
				// send command to DVR Bridge Service servers to stop recording stream
				// if this fails the dvr link will be removed
				$model->stopDvrs();
				
				// record the time that the stream is being marked as over
				$model->end_time = Carbon::now();

				// run these once the response has been sent to the user just before the script ends
				// this makes sure if this is currently in a transaction the transaction will have ended
				Event::listen('app.finish', function() use (&$model) {
					Event::fire('mediaItemLiveStream.showOver', array($model));
				});
			}
			
			if ($model->hasJustBecomeNotLive() && ($model->hasJustLeftLive() || $model->hasJustLeftStreamOver())) {
				// send command to DVR Bridge Service servers to stop recording stream
				// and inform it that the recording can be deleted.
				// entry will be removed from dvr_stream_uris table
				$model->removeDvrs();

				// run these once the response has been sent to the user just before the script ends
				// this makes sure if this is currently in a transaction the transaction will have ended
				Event::listen('app.finish', function() use (&$model) {
					Event::fire('mediaItemLiveStream.notLive', array($model));
				});
			}
			
			if ($model->liveStreamHasChanged()) {
				// if the live stream attached to this media item live stream has just been changed
				// then remove any dvr links as they will be to the previous live stream
				$model->removeDvrs();
			}
			
			return true;
		});
		
		self::saved(function($model) {
			
			// this can't be in the saving callback because this needs to have an id so it can be associated with a MediaItemLiveStream,
			// and if this is the first ever save because the model is being created it won't have an id yet.
			if ($model->hasJustBecomeLive()) {
				// send command to DVR Bridge Service servers to start recording stream
				// and create entry in dvr_stream_uris table
				$model->startDvrs();
			}
			
			// transaction starts in save event
			DB::commit();
			
			if ($model->hasJustBecomeLive()) {
				// run these once the response has been sent to the user just before the script ends
				// this makes sure if this is currently in a transaction the transaction will have ended
				Event::listen('app.finish', function() use (&$model) {
					Event::fire('mediaItemLiveStream.live', array($model));
					Queue::push("uk\co\la1tv\website\jobs\MediaItemLiveEmailsJob", array("mediaItemId"=>intval($model->mediaItem->id)));
				});
			}
		});
	}
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
	
	public function dvrLiveStreamUris() {
		return $this->hasMany(self::$p.'DvrLiveStreamUri', 'media_item_live_stream_id');
	}
	
	public function stateDefinition() {
		return $this->belongsTo(self::$p.'LiveStreamStateDefinition', 'state_id');
	}
	
	
	// if the state is set to "live" but there is no live stream attached to, or the attached live stream is not live, then the resolved version is "Not Live"
	// the exception is if there is an external live stream url in which case a stream can be marked as live even if there no live stream attached or the attached live stream is inaccessible
	public function getResolvedStateDefinition($stateDefinitionParam=null) {
		$stateDefinition = is_null($stateDefinitionParam) ? $this->stateDefinition : $stateDefinitionParam;
		if (is_null($this->external_stream_url) && intval($stateDefinition->id) === 2 && (is_null($this->liveStream) || !$this->liveStream->getIsAccessible())) {
			// set to "live" but no live stream attached or live. Pretend "Not Live"
			return LiveStreamStateDefinition::find(1);
		}
		return $stateDefinition;
	}

	public function getViewCount() {
		return PlaybackHistory::getStreamViewCount(intval($this->media_item_id));
	}
	
	// returns true if this should be shown with the parent media item. If false then it should like the MediaItem does not have a live stream component.
	// this can still return true even if there is no LiveStream model associated with this.
	// getResolvedStateDefinition() should be used to determine the state of the actual stream.
	public function getIsAccessible() {
		return $this->enabled && $this->mediaItem->getIsAccessible();
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->whereHas("mediaItem", function($q2) {
			$q2->accessible();
		});
	}
	
	public function hasDvrRecording() {
		return $this->dvrLiveStreamUris()->count() > 0;
	}
	
	public function hasWatchableContent() {
		return $this->isLive() || ($this->isOver() && $this->hasDvrRecording());
	}
	
	public function isNotLive($stateDefinition=null) {
		return intval($this->getResolvedStateDefinition($stateDefinition)->id) === 1;
	}
	
	public function isLive($stateDefinition=null) {
		return intval($this->getResolvedStateDefinition($stateDefinition)->id) === 2;
	}
	
	public function isOver($stateDefinition=null) {
		return intval($this->getResolvedStateDefinition($stateDefinition)->id) === 3;
	}
	
	// not live when has state_id of 1 or when has live state id but the live stream is nonexistent or inaccessible
	public function scopeNotLive($q, $yes=true) {
		// outer where is needed because otherwise the query that eloquent generates gets messed up when the or is used
		return $q->where(function($q) use (&$yes) {
			$q = $q->where("state_id", $yes ? "=" : "!=", 1);
			if ($yes) {
				$q->orWhere(function($q2) {
					$q2->whereNull("external_stream_url")
					->where("state_id", 2)
					->whereHas("liveStream", function($q3) {
						$q3->accessible();
					}, "=", 0);
				});
			}
		});
	}
	
	public function scopeLive($q, $yes=true) {
		// outer where is needed because otherwise the query that eloquent generates gets messed up when the or is used
		return $q->where(function($q) use (&$yes) {
			if ($yes) {
				$q->where(function($q2) {
					$q2->whereNotNull("external_stream_url")
					->orWhereHas("liveStream", function($q3) {
						$q3->accessible();
					});
				});
			}
			$q = $q->where("state_id", $yes ? "=" : "!=", 2);
		});
	}
	
	public function scopeShowOver($q, $yes=true) {
		return $q->where("state_id", $yes ? "=" : "!=", 3);
	}
	
	public function scopeHasDvrRecording($q, $yes=true) {
		return $q->has("dvrLiveStreamUris", $yes ? ">" : "=", 0);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('end_time'));
	}
	
	// has just become "live"
	public function hasJustBecomeLive() {
		return $this->isLive() && (!$this->exists || !$this->isLive(LiveStreamStateDefinition::find($this->getOriginal("state_id", 1))));
	}
	
	// has just left "live"
	public function hasJustLeftLive() {
		return !$this->isLive() && $this->exists && $this->isLive(LiveStreamStateDefinition::find($this->getOriginal("state_id", 1)));
	}
	
	// has just become "stream over"
	public function hasJustBecomeStreamOver() {
		return $this->isOver() && (!$this->exists || !$this->isOver(LiveStreamStateDefinition::find($this->getOriginal("state_id", 1))));
	}
	
	// has just left "stream over"
	public function hasJustLeftStreamOver() {
		return !$this->isOver() && $this->exists && $this->isOver(LiveStreamStateDefinition::find($this->getOriginal("state_id", 1)));
	}
	
	// has just become "not live"
	public function hasJustBecomeNotLive() {
		return $this->isNotLive() && (!$this->exists || !$this->isNotLive(LiveStreamStateDefinition::find($this->getOriginal("state_id", 1))));
	}
	
	// has just left "not live"
	public function hasJustLeftNotLive() {
		return !$this->isNotLive() && $this->exists && $this->isNotLive(LiveStreamStateDefinition::find($this->getOriginal("state_id", 1)));
	}
	
	// the live stream linked to this has changed from what is currently in the database
	public function liveStreamHasChanged() {
		return !$this->exists || $this->getOriginal("live_stream_id") !== $this->live_stream_id;
	}
	
	public function getQualitiesWithUris($filters=null) {
		$liveStreamModel = $this->liveStream;
		if (is_null($liveStreamModel)) {
			return array();
		}
		return $liveStreamModel->getQualitiesWithUris($filters, $this);
	}
	
	// returns an array containing all the domains that live streams come from which are loaded from a http request. I.e. playlist.m3u8 for mobiles.
	public static function getCachedLiveStreamDomains() {
		return Cache::remember('liveStreamDomains', Config::get("custom.live_stream_domains_cache_time"), function() {
			$uris = array();
			$models = self::get();
			foreach($models as $a) {
				foreach($a->getQualitiesWithUris(array("live", "nativeDvr", "dvrBridge")) as $b) {
					foreach($b['uris'] as $uri) {
						$uris[] = $uri['uri'];
					}
				}
			}
			
			$uris = array_where($uris, function($key, $value) {
				// filter out so we only have uris beginning with http:// or https://
				return preg_match('@^https?://@i', $value) === 1;
			});
			$domains = array();
			foreach($uris as $a) {
				$info = parse_url($a);
				$domain = $info['scheme']."://".$info['host'];
				if (isset($info['port'])) {
					$domain .= ":".$info['port'];
				}
				$domains[] = $domain;
			}
			$domains = array_unique($domains);
			return $domains;
		});
	}
	
	// sent the START command to any dvr bridge service urls.
	// this method is private because this should only be called when the stream state changes to live
	private function startDvrs() {
		$liveStreamModel = $this->liveStream;
		if (is_null($liveStreamModel)) {
			// could be null if just external stream on external site
			return;
		}
		
		// there shouldn't already be one in progress but clear anyway to be sure
		$this->dvrLiveStreamUris()->delete();
		
		foreach($liveStreamModel->liveStreamUris as $uriModel) {
			$enabled = (boolean) $uriModel->enabled;
			$uriForDvrBridgeService = (boolean) $uriModel->dvr_bridge_service_uri;
			if ($enabled && $uriForDvrBridgeService) {
				$dvrLiveStreamUriModel = new DvrLiveStreamUri();
				$dvrLiveStreamUriModel->liveStreamUri()->associate($uriModel);
				$dvrLiveStreamUriModel->mediaItemLiveStream()->associate($this);
				$this->dvrLiveStreamUris()->save($dvrLiveStreamUriModel);
				
				$responseInfo = self::makeDvrBridgeServiceRequest($uriModel->uri, "START", $dvrLiveStreamUriModel->id);
				if ($responseInfo["statusCode"] === 200 && !is_null($responseInfo['data']) && !is_null($responseInfo['data']['url'])) {
					// success. dvr has started
					// add url to dvr_live_stream_uris entry
					$dvrLiveStreamUriModel->uri = $responseInfo['data']['url'];
					$dvrLiveStreamUriModel->save();
				}
				else {
					$dvrLiveStreamUriModel->delete();
				}
			}
		}
		// there may be new domains now from the urls retrieved from the dvr bridge services
		Cache::forget("liveStreamDomains");
	}
	
	// sent the STOP command to any dvr bridge service urls
	private function stopDvrs() {
		foreach($this->dvrLiveStreamUris as $dvrLiveStreamUriModel) {
			$liveStreamUriModel = $dvrLiveStreamUriModel->liveStreamUri;
			$responseInfo = self::makeDvrBridgeServiceRequest($liveStreamUriModel->uri, "STOP", $dvrLiveStreamUriModel->id);
			if ($responseInfo["statusCode"] !== 200) {
				// error occurred. Remove dvrLiveStreamUri. This will cause pings to stop so the dvr bridge service server should sort itself out
				$dvrLiveStreamUriModel->delete();
			}
		}
	}
	
	// sent the REMOVE command to any dvr bridge service urls
	// this method is public because it is ok to stop and remove the dvr recordings from other places
	// dvr recordings can be removed at any time without causing issues.
	public function removeDvrs() {
		foreach($this->dvrLiveStreamUris as $dvrLiveStreamUriModel) {
			$liveStreamUriModel = $dvrLiveStreamUriModel->liveStreamUri;
			// don't care if there's an error because pings will stop, meaning the dvr bridge server should sort itself out anyway
			self::makeDvrBridgeServiceRequest($liveStreamUriModel->uri, "REMOVE", $dvrLiveStreamUriModel->id);
			$dvrLiveStreamUriModel->delete();
		}
	}
	
	// make a request to a dvr bridge service and return the response as an array of array("statusCode", "data")
	// statusCode will be null if there was an error getting the response, data will be null if the status code is not 200, or if invalid json response
	public static function makeDvrBridgeServiceRequest($url, $commandType, $id, $requestTimeout=6000) {

		$scopedId = Config::get("dvrBridgeService.idPrefix").$id;

		$data = array("type"=>$commandType, "id"=>$scopedId);
		$encodedData = http_build_query($data);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $requestTimeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Length: ' . strlen($encodedData))
		);

		$result = curl_exec($ch);
		
		if (curl_errno($ch) > 0) {
			// curl error, possibly timeout
			curl_close($ch);
			return array("statusCode"=>null, "data"=>null);
		}
		
		$responseStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($responseStatusCode === 200) {
			$responseData = json_decode($result, true);
			return array("statusCode"=>$responseStatusCode, "data"=>$responseData);
		}
		else {
			return array("statusCode"=>$responseStatusCode, "data"=>null);
		}
	}
}
