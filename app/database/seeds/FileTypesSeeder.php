<?php

use uk\co\la1tv\website\models\FileType;
use uk\co\la1tv\website\models\FileExtension;

class FileTypesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$fileTypes = array(
			array("id"=>1, "description"=>"For side banner source images.", "extensions"=>array(1, 2, 3), "mimeType"=>null),
			array("id"=>2, "description"=>"For cover source images.", "extensions"=>array(1, 2, 3), "mimeType"=>null),
			array("id"=>3, "description"=>"For vod video source uploads.", "extensions"=>array(4), "mimeType"=>null),
			array("id"=>4, "description"=>"For cover art source images.", "extensions"=>array(1, 2, 3), "mimeType"=>null),
			array("id"=>5, "description"=>"For side banner image renders.", "extensions"=>array(), "mimeType"=>null),
			array("id"=>6, "description"=>"For cover image renders.", "extensions"=>array(), "mimeType"=>null),
			array("id"=>7, "description"=>"For vod video renders.", "extensions"=>array(), "mimeType"=>null),
			array("id"=>8, "description"=>"For cover art image renders.", "extensions"=>array(), "mimeType"=>null),
			array("id"=>9, "description"=>"For vod scrub thumbnails.", "extensions"=>array(), "mimeType"=>null),
			array("id"=>10, "description"=>"For side banner fill source images.", "extensions"=>array(1, 2, 3), "mimeType"=>null),
			array("id"=>11, "description"=>"For side banner fill image renders.", "extensions"=>array(), "mimeType"=>null),
			array("id"=>12, "description"=>"For dash media presentation description files.", "extensions"=>array(), "mimeType"=>"application/dash+xml"),
			array("id"=>13, "description"=>"For dash segment files.", "extensions"=>array(), "mimeType"=>null),
			array("id"=>15, "description"=>"For hls media playlist files.", "extensions"=>array(), "mimeType"=>"application/x-mpegURL"),
			array("id"=>16, "description"=>"For hls segment files.", "extensions"=>array(), "mimeType"=>"video/MP2T"),
		);
		
		foreach($fileTypes as $a) {
			$data = array("id"=>$a['id'], "description"=>$a['description'], "mime_type"=>$a['mimeType']);
			
			$f = FileType::with("extensions")->find($a['id']);
			if ($f !== NULL) {
				DB::transaction(function() use (&$a, &$f, &$data) {
					$f->update($data);
					$toAdd = $a['extensions'];
					foreach($f->extensions as $b) {
						if (in_array($b->id, $a['extensions'])) {
							if(($key = array_search($b->id, $toAdd)) !== false) {
								unset($toAdd[$key]);
							}
						}
						else {
							$f->extensions()->detach($b);
						}
					}
					foreach($toAdd as $b) {
						$f->extensions()->attach(FileExtension::find($b));
					}
				});
			}
			else {
				DB::transaction(function() use (&$a, &$data) {
					$f = FileType::create($data);
					// can't use above $f because of what seems to be a bug. At the moment the id property on the above is always 0
					$f = FileType::find($data['id']);
					foreach($a['extensions'] as $b) {
						$f->extensions()->attach(FileExtension::find($b));
					}
				});
			}
		}
		$this->command->info('File types created/updated!');
	}


}
