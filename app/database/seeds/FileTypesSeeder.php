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
			array("id"=>1, "description"=>"For side banner source images.", "extensions"=>array(1, 2)),
			array("id"=>2, "description"=>"For cover source images.", "extensions"=>array(1, 2)),
			array("id"=>3, "description"=>"For vod video source uploads.", "extensions"=>array(4)),
			array("id"=>4, "description"=>"For cover art source images.", "extensions"=>array(1, 2)),
			array("id"=>5, "description"=>"For vod video renders.", "extensions"=>array()),
			array("id"=>6, "description"=>"For side banner image renders.", "extensions"=>array()),
			array("id"=>7, "description"=>"For cover image renders.", "extensions"=>array()),
			array("id"=>8, "description"=>"For cover art image renders.", "extensions"=>array()),
		);
		
		foreach($fileTypes as $a) {
			$data = array("id"=>$a['id'], "description"=>$a['description']);
			
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
