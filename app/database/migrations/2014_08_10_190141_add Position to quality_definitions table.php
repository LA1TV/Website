
<!-- saved from url=(0410)https://raw.githubusercontent.com/LA1TV/Website/master/app/database/migrations/2014_08_10_190141_add%20Position%20to%20quality_definitions%20table.php?token=3259993__eyJzY29wZSI6IlJhd0Jsb2I6TEExVFYvV2Vic2l0ZS9tYXN0ZXIvYXBwL2RhdGFiYXNlL21pZ3JhdGlvbnMvMjAxNF8wOF8xMF8xOTAxNDFfYWRkIFBvc2l0aW9uIHRvIHF1YWxpdHlfZGVmaW5pdGlvbnMgdGFibGUucGhwIiwiZXhwaXJlcyI6MTQwODgwMDUzNn0%3D--7982179ab4cf0e45ac89434ac7dcf69d8d9d0a5a -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">&lt;?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionToQualityDefinitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('quality_definitions', function(Blueprint $table)
		{
			$table-&gt;tinyInteger("position")-&gt;unique("position_unique_index");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('quality_definitions', function(Blueprint $table)
		{
			$table-&gt;dropColumn("position");
		});
	}

}
</pre></body></html>