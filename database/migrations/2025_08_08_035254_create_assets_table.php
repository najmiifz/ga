<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('tipe');
            $table->string('jenis_aset');
            $table->string('pic');
            $table->string('merk');
            $table->string('nomor_sn');
            $table->string('project');
            $table->string('lokasi');
            $table->integer('tahun_beli');
            $table->bigInteger('harga_beli');
            $table->bigInteger('harga_sewa');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
