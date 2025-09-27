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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->nullable();
            $table->integer('admin_id')->nullable();

            //IBU HAMIL
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('nama_suami')->nullable();
            $table->string('telepon_suami')->nullable();
            $table->integer('usia_suami')->nullable();
            $table->string('pekerjaan_suami')->nullable();
            $table->integer('usia_kehamilan')->nullable();
            $table->integer('saldo_total')->nullable()->default(0);
            $table->unsignedBigInteger('selected_icon_id')->nullable();
            $table->string('selected_icon_name_cache')->nullable();
            $table->string('selected_icon_data_cache')->nullable();
            $table->foreign('selected_icon_id')    
                  ->references('id')            
                  ->on('icons')
                  ->onDelete('set null');
            // $table->integer('jumlah_kehamilan_sebelumnya')->nullable();
            // $table->integer('jumlah_anak_hidup')->nullable();
            // $table->date('hpht')->nullable();
            // $table->text('riwayat_penyakit_ibu')->nullable();
            // $table->text('riwayat_alergi')->nullable();
            // $table->string('riwayat_imunisasi_tt')->nullable();
            // $table->text('riwayat_kehamilan_sebelumnya')->nullable();
            // $table->boolean('kehamilan_direncanakan')->nullable();
            // $table->string('kontrasepsi_sebelum_hamil')->nullable();
            // $table->string('nomor_kk')->nullable();
            // $table->string('nomor_bpjs')->nullable();
            // $table->string('preferensi_faskes')->nullable();
            // $table->string('nama_kontak_darurat')->nullable();
            // $table->string('telepon_kontak_darurat')->nullable();

            //BIDAN
            $table->string('nomor_str')->unique()->nullable();
            $table->date('masa_berlaku_str')->nullable();
            $table->string('nomor_sipb')->unique()->nullable();
            $table->date('masa_berlaku_sipb')->nullable();
            $table->string('tempat_praktik')->nullable();
            $table->text('alamat_praktik')->nullable();
            $table->string('telepon_tempat_praktik')->nullable();
            $table->string('spesialisasi')->nullable();
            $table->string('nik')->nullable();

            //DINKES
              $table->string('nama_dinas')->nullable();
              $table->text('alamat_kantor')->nullable();
              $table->string('website')->nullable();
              $table->string('logo')->nullable();
              $table->string('nama_admin')->nullable();
              $table->string('nip')->nullable();
              $table->string('jabatan')->nullable();
              $table->string('foto_ktp')->nullable();

              $table->rememberToken();
              $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
