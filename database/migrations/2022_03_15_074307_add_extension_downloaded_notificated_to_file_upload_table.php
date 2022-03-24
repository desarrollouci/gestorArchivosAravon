<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtensionDownloadedNotificatedToFileUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fileUpload', function (Blueprint $table) {
            /*$table->string('extension')->after('filename');
            $table->boolean('downloaded')->default(0)->after('extension');
            $table->boolean('notificated')->default(0)->after('downloaded');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fileUpload', function (Blueprint $table) {
            //
        });
    }
}
