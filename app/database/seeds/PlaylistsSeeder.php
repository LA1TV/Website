<?php

use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\Series;
use uk\co\la1tv\website\models\MediaItem;

class PlaylistsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		// presumes that media items already exist and ids from autoincrement
		$rosesSeriesId = NULL;
		DB::transaction(function() use (&$rosesSeriesId) {
			$playlist = new Playlist(array(
					"name"	=>	"Roses 2014!",
					"enabled"	=> true,
					"description"	=> "Description about roses 2014 series.",
					"series_no"		=> 1
			));
			$playlist->series()->associate(Series::find(1));
			$playlist->save();
			$rosesSeriesId = $playlist->id;
			$playlist->mediaItems()->attach(MediaItem::find(1), array("position"=>0));
			$playlist->mediaItems()->attach(MediaItem::find(2), array("position"=>1));
		});
		
		DB::transaction(function() use (&$rosesSeriesId) {
			$playlist = Playlist::create(array(
					"name"	=>	"Top Shows",
					"enabled"	=> true,
					"description"	=> "LA1:TV's top shows for 2014."
			));
			$playlist->mediaItems()->attach(MediaItem::find(2), array("position"=>0, "from_playlist_id"=>$rosesSeriesId));
		});
		
		$this->command->info('Playlists created and media items added!');
	}

}
