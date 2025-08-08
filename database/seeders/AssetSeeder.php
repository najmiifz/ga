<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak duplikat saat seeder dijalankan ulang
        Asset::truncate();
        User::truncate();

        // Buat User Admin dan Pengguna
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'role' => 'pengguna',
        ]);

        // Data Aset Awal
        $assets = [
            ['id' => 1, 'tipe' => 'Kendaraan', 'jenis_aset' => 'Mobil', 'pic' => 'Nicolas Abraham', 'merk' => 'Innova Zenix', 'nomor_sn' => 'D 1338 ALF', 'project' => 'Head Office', 'lokasi' => 'Bandung', 'tahun_beli' => 2024, 'harga_beli' => 510000000, 'harga_sewa' => 10186667],
            ['id' => 2, 'tipe' => 'Kendaraan', 'jenis_aset' => 'Mobil', 'pic' => 'Available', 'merk' => 'Toyota Avanza', 'nomor_sn' => 'D 1290 II', 'project' => 'Moratel', 'lokasi' => 'Indramayu', 'tahun_beli' => 2011, 'harga_beli' => 210798000, 'harga_sewa' => 3513167],
            ['id' => 3, 'tipe' => 'Kendaraan', 'jenis_aset' => 'Mobil', 'pic' => 'Sudarmanto', 'merk' => 'Daihatsu Grand Max', 'nomor_sn' => 'D 8172 OC', 'project' => 'Head Office', 'lokasi' => 'Bandung', 'tahun_beli' => 2011, 'harga_beli' => 102858000, 'harga_sewa' => 1714467],
            ['id' => 4, 'tipe' => 'Kendaraan', 'jenis_aset' => 'Mobil', 'pic' => 'Available', 'merk' => 'Daihatsu Ayla', 'nomor_sn' => 'D 1955 ABE', 'project' => 'Icon', 'lokasi' => 'Malang', 'tahun_beli' => 2013, 'harga_beli' => 124013000, 'harga_sewa' => 2066883],
            ['id' => 5, 'tipe' => 'Kendaraan', 'jenis_aset' => 'Mobil', 'pic' => 'Fariq Kunta Muzaki', 'merk' => 'Daihatsu Ayla', 'nomor_sn' => 'D 1151 ABG', 'project' => 'EMR', 'lokasi' => 'Lampung', 'tahun_beli' => 2013, 'harga_beli' => 122978000, 'harga_sewa' => 2049633],
            ['id' => 6, 'tipe' => 'Kendaraan', 'jenis_aset' => 'Mobil', 'pic' => 'Bagus Ryan Permana', 'merk' => 'Daihatsu Xenia', 'nomor_sn' => 'D 1287 ABG', 'project' => 'Moratel', 'lokasi' => 'Serang', 'tahun_beli' => 2013, 'harga_beli' => 167901000, 'harga_sewa' => 2798350],
            ['id' => 7, 'tipe' => 'Kendaraan', 'jenis_aset' => 'Mobil', 'pic' => 'Iman Sukman', 'merk' => 'Daihatsu Xenia', 'nomor_sn' => 'D 1572 ABG', 'project' => 'Linknet', 'lokasi' => 'Bekasi', 'tahun_beli' => 2013, 'harga_beli' => 167901000, 'harga_sewa' => 2798350],
            ['id' => 8, 'tipe' => 'Elektronik', 'jenis_aset' => 'Laptop', 'pic' => 'Rina', 'merk' => 'Macbook Pro', 'nomor_sn' => 'LP001', 'project' => 'Head Office', 'lokasi' => 'Bandung', 'tahun_beli' => 2023, 'harga_beli' => 25000000, 'harga_sewa' => 1500000],
            ['id' => 9, 'tipe' => 'Elektronik', 'jenis_aset' => 'Laptop', 'pic' => 'Doni', 'merk' => 'Dell XPS', 'nomor_sn' => 'LP002', 'project' => 'EMR', 'lokasi' => 'Lampung', 'tahun_beli' => 2023, 'harga_beli' => 22000000, 'harga_sewa' => 1200000],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}
