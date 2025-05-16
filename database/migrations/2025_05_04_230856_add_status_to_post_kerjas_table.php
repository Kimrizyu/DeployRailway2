<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('post_kerjas', function (Blueprint $table) {
            $table->string('status')->default('active'); // Status default aktif
        });
    }

    public function down()
    {
        Schema::table('post_kerjas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

};
