<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->integer('duration')->nullable()->after('price')->comment('Durasi dalam menit');
        });
    }

    public function down()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
