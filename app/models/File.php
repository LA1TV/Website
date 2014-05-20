<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class File extends Eloquent {

	protected $table = 'files';
	protected $fillable = array('in_use');
	
}