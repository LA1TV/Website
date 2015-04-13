<?php

use uk\co\la1tv\website\models\FileType;
use uk\co\la1tv\website\models\UploadPoint;

class UploadPointsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$uploadPoints = array(
			array("id"=>1, "description"=>"For side banner images.", "fileTypeId"=>1),
			array("id"=>2, "description"=>"For cover images.", "fileTypeId"=>2),
			array("id"=>3, "description"=>"For vod video uploads.", "fileTypeId"=>3),
			array("id"=>4, "description"=>"For cover art uploads.", "fileTypeId"=>4),
			array("id"=>5, "description"=>"For side banner fill images.", "fileTypeId"=>10)
		);
		
		foreach($uploadPoints as $b=>$a) {
			$fileType = FileType::find($a['fileTypeId']);
			unset($a['fileTypeId']);
			$p = UploadPoint::find($a['id']);
			if (is_null($p)) {
				$p = new UploadPoint($a);
				$p->fileType()->associate($fileType);
			}
			else {
				$p->fileType()->associate($fileType);
			}
			$p->save();
		}
		$this->command->info('Upload points created/updated!');
	}


}
