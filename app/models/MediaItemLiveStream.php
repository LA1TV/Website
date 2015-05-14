<?php namespace uk\co\la1tv\website\models;

use \Session as SessionProvider;
use Carbon;
use Config;
use Queue;
use Event;
use DB;
use Cache;
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
			
			if ($model->hasJustBecomeLive()) {
				// TODO description
				$model->startDvrs();
			}
			
			if ($model->hasJustBecomeStreamOver()) {
				// TODO dvr bridge service stop api call
				// if fails set urls to null
				// TODO description
				$model->stopDvrs();
				
				// record the time that the stream is being marked as over
				$model->end_time = Carbon::now();
			}
			
			if ($model->hasJustBecomeNotLive()) {
				// TODO description
				$model->removeDvrs();
			}
			
			
			return true;
		});
		
		self::saved(function($model) {
			
			// transaction starts in save event
			DB::commit();
			
			if ($model->hasJustBecomeLive()) {
				// queue the email job once the response has been sent to the user just before the script ends
				// this makes sure if this is currently in a transaction the transaction will have ended when the job is queued
				Event::listen('app.finish', function() use (&$model) {
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
	
	public function registerViewCount() {	
		if (!$this->getIsAccessible() || intval($this->getResolvedStateDefinition()->id) !== 2) {
			// shouldn't be accessible or stream not live
			return;
		}
	
		$sessionKey = "viewCount-".$this->id;
		$lastTimeRegistered = SessionProvider::get($sessionKey, null);
		if (!is_null($lastTimeRegistered) && $lastTimeRegistered >= Carbon::now()->subMinutes(Config::get("custom.interval_between_registering_view_counts"))->timestamp) {
			// already registered view not that long ago.
			return;
		}
		$this->increment("view_count");
		SessionProvider::set($sessionKey, Carbon::now()->timestamp);
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
	
	public function isNotLive() {
		return intval($this->getResolvedStateDefinition()->id) === 1;
	}
	
	public function isLive($stateDefinition=null) {
		return intval($this->getResolvedStateDefinition($stateDefinition)->id) === 2;
	}
	
	public function isOver() {
		return intval($this->getResolvedStateDefinition()->id) === 3;
	}
	
	// not live when has state_id of 1 or when has live state id but the live stream is nonexistent or inaccessible
	public function scopeNotLive($q, $yes=true) {
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
		
		return $q;
	}
	
	public function scopeLive($q, $yes=true) {
		if ($yes) {
			$q->where(function($q2) {
				$q2->whereNotNull("external_stream_url")
				->orWhereHas("liveStream", function($q3) {
					$q3->accessible();
				});
			});
		}
		$q = $q->where("state_id", $yes ? "=" : "!=", 2);
		return $q;
	}
	
	public function scopeShowOver($q, $yes=true) {
		return $q->where("state_id", $yes ? "=" : "!=", 3);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('end_time'));
	}
	
	// has gone from "not live" or "stream over" to "live"
	public function hasJustBecomeLive() {
		return $this->isLive() && (!$this->exists || !$this->isLive(LiveStreamStateDefinition::find($this->original["state_id"])));
	}
	
	// has gone from "live" to "stream over"
	public function hasJustBecomeStreamOver() {
		return $this->isOver() && $this->exists && $this->isLive(LiveStreamStateDefinition::find($this->original["state_id"]));
	}
	
	// has gone from "live" to "not live"
	public function hasJustBecomeNotLive() {
		return $this->isNotLive() && $this->exists && $this->isLive(LiveStreamStateDefinition::find($this->original["state_id"]));
	}
	
	public function getQualitiesWithUris($dvrUrisOnly=false) {
		$liveStreamModel = $this->liveStream;
		if (is_null($liveStreamModel)) {
			return array();
		}
		
		$qualities = array();
		$urisOrganisedByQuality = $liveStreamModel->getUrisOrganisedByQuality();
		foreach($urisOrganisedByQuality as $quality) {
			$entry = array(
				"qualityDefinition"	=> $quality["qualityDefinition"],
				"uris"				=> array()
			);
			
			foreach($quality['uris'] as $uriAndInfo) {
				if (!$uriAndInfo['enabled']) {
					continue;
				}
				
				// if the url is a url for a dvr bridge service then the url the user gets will be the url it returns
				// the url it returns will be a hls url with dvr support.
				$uriWithDvrSupport = $uriAndInfo['uriForDvrBridgeService'];
				// if a dvr bridge service is being used then the url it provides will be placed in uri_from_dvr_bridge_service
				// this may be null if there's been an error, in which case the user should not see it
				$uri = null;
				if(!$uriWithDvrSupport) {
					$uri = $uriAndInfo['uri'];
				}
				else {
					$dvrLiveStreamUriModel = $uriAndInfo["liveStreamUriModel"]->dvrLiveStreamUris()->where("dvr_live_stream_uris.media_item_live_stream_id", $this->id)->first();
					if (!is_null($dvrLiveStreamUriModel)) {
						$uri = $dvrLiveStreamUriModel->uri;
					}
				}
				
				if (is_null($uri)) {
					continue;
				}
				
				if ($dvrUrisOnly && !$uriWithDvrSupport) {
					continue;
				}
			
				$entry['uris'][] = array(
					"uri"	=> $uri,
					"uriWithDvrSupport"	=> $uriWithDvrSupport,
					"type"	=> $uriAndInfo['type'],
					"supportedDevices"	=> $uriAndInfo['supportedDevices']
				);
			
			}
			
			if (count($entry["uris"]) > 0) {
				$qualities[] = $entry;
			}
		}
		return $qualities;
	}
	
	// returns an array containing all the domains that live streams come from which are loaded from a http request. I.e. playlist.m3u8 for mobiles.
	public static function getCachedLiveStreamDomains() {
		return Cache::remember('liveStreamDomains', Config::get("custom.live_stream_domains_cache_time"), function() {
			$uris = array();
			$models = self::get();
			foreach($models as $a) {
				foreach($a->getQualitiesWithUris() as $b) {
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
	private function startDvrs() {
		$liveStreamModel = $this->liveStream;
		if (is_null($liveStreamModel)) {
			// could be null if just external stream on external site
			return;
		}
		foreach($liveStreamModel->liveStreamUris as $uriModel) {
			$enabled = (boolean) $uriModel->enabled;
			$uriForDvrBridgeService = (boolean) $uriModel->dvr_bridge_service_uri;
			if ($enabled && $uriForDvrBridgeService) {
				// there shouldn't already be one in progress but clear anyway to be sure
				DB::transaction(function() {
					$this->dvrLiveStreamUris()->delete();
				});
				$responseInfo = $this->makeDvrBridgeServiceRequest($uriModel->uri, "START", $this->id);
				if ($responseInfo["statusCode"] === 200 && !is_null($responseInfo['data']) && !is_null($responseInfo['data']['url'])) {
					// success. dvr has started
					// add entry to dvr_live_stream_uris table
					$dvrLiveStreamUriModel = new DvrLiveStreamUri(array(
						"uri"	=> $responseInfo['data']['url']
					));
					$dvrLiveStreamUriModel->liveStreamUri()->associate($uriModel);
					$dvrLiveStreamUriModel->mediaItemLiveStream()->associate($this);
					$this->dvrLiveStreamUris()->save($dvrLiveStreamUriModel);
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
			$responseInfo = $this->makeDvrBridgeServiceRequest($liveStreamUriModel->uri, "STOP", $this->id);
			if ($responseInfo["statusCode"] !== 200) {
				// error occurred. Remove dvrLiveStreamUri. This will cause pings to stop so the dvr bridge service server should sort itself out
				$dvrLiveStreamUriModel->delete();
			}
		}
	}
	
	// sent the REMOVE command to any dvr bridge service urls
	private function removeDvrs() {
		foreach($this->dvrLiveStreamUris as $dvrLiveStreamUriModel) {
			$liveStreamUriModel = $dvrLiveStreamUriModel->liveStreamUri;
			// don't care if there's an error because pings will stop, meaning the dvr bridge server should sort itself out anyway
			$this->makeDvrBridgeServiceRequest($liveStreamUriModel->uri, "REMOVE", $this->id);
			$dvrLiveStreamUriModel->delete();
		}
	}
	
	// make a request to a dvr bridge service and return the response as an array of array("statusCode", "data")
	// statusCode will be null if there was an error getting the response, data will be null if the status code is not 200, or if invalid json response
	private function makeDvrBridgeServiceRequest($url, $commandType, $id) {
		$data = array("type"=>$commandType, "id"=>$id);
		$encodedData = http_build_query($data);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1500); // timeout if takes longer than 1.5 seconds
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
