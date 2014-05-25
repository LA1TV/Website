<?php namespace uk\co\la1tv\website\controllers\home\admin\comments;

use View;

class CommentsController extends CommentsBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.comments.index'), "comments", "comments");
	}
}
