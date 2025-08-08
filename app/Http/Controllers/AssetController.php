<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AssetController extends Controller
{
    // Menampilkan halaman utama manajemen aset
    public function index(Request $request)
    {
        $query = Asset::query();

        // Filter
        if ($request->filled('search_pic')) {
            $query->where('pic', 'like', '%' . $request->search_pic . '%');
        }
        if ($request->filled('filter_tipe') && $request->filter_tipe != 'semua') {
            $query->where('tipe', $request->filter_tipe);
        }
        if ($request->filled('filter_project') && $request->filter_project != 'semua') {
            $query->where('project', $request->filter_project);
        }
        if ($request->filled('filter_lokasi') && $request->filter_lokasi != 'semua') {
            $query->where('lokasi', $request->filter_lokasi);
        }
        if ($request->filled('filter_jenis') && $request->filter_jenis != 'semua') {
            $query->where('jenis_aset', $request->filter_jenis);
        }

        // Sorting
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        } else {
            $query->orderBy('id', 'desc'); // Default sort
        }

        // Ambil data untuk filter dropdowns
        $filters = [
            'tipe' => Asset::select('tipe')->distinct()->pluck('tipe'),
            'project' => Asset::select('project')->distinct()->pluck('project'),
            'lokasi' => Asset::select('lokasi')->distinct()->pluck('lokasi'),
            'jenis' => Asset::select('jenis_aset')->distinct()->pluck('jenis_aset'),
        ];

        $assets = $query->paginate(5)->withQueryString(); // 5 item per halaman

        return view('assets.index', compact('assets', 'filters'));
    }

    // Menyediakan data untuk dashboard dan filter
    public function getDashboardData()
    {
        $allAssets = Asset::all();

        $totalValue = $allAssets->sum('harga_beli');
        $totalAssets = $allAssets->count();
        $usedAssets = $allAssets->where('pic', '!=', 'Available')->count();
        $availableAssets = $totalAssets - $usedAssets;

        $typeCounts = $allAssets->groupBy('jenis_aset')->map->count();
        $projectCounts = $allAssets->groupBy('project')->map->count();
        $locationBreakdown = $allAssets->groupBy('lokasi')->map(function ($group) {
            return [
                'count' => $group->count(),
                'value' => $group->sum('harga_beli')
            ];
        });

        return response()->json([
            'totalValue' => $totalValue,
            'totalAssets' => $totalAssets,
            'usedAssets' => $usedAssets,
            'availableAssets' => $availableAssets,
            'typeChart' => ['labels' => $typeCounts->keys(), 'data' => $typeCounts->values()],
            'projectChart' => ['labels' => $projectCounts->keys(), 'data' => $projectCounts->values()],
            'locationBreakdown' => $locationBreakdown,
        ]);
    }

    // Menyimpan aset baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipe' => 'required|string|max:255',
            'jenis_aset' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'nomor_sn' => 'required|string|max:255|unique:assets,nomor_sn',
            'project' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tahun_beli' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_sewa' => 'required|numeric',
        ]);

        $asset = Asset::create($validatedData);
        return response()->json(['message' => 'Aset berhasil ditambahkan!', 'asset' => $asset]);
    }

    // Mengambil data satu aset untuk form edit
    public function show(Asset $asset)
    {
        return response()->json($asset);
    }

    // Memperbarui aset
    public function update(Request $request, Asset $asset)
    {
        $validatedData = $request->validate([
            'tipe' => 'required|string|max:255',
            'jenis_aset' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'nomor_sn' => 'required|string|max:255|unique:assets,nomor_sn,' . $asset->id,
            'project' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tahun_beli' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_sewa' => 'required|numeric',
        ]);

        $asset->update($validatedData);
        return response()->json(['message' => 'Aset berhasil diperbarui!', 'asset' => $asset]);
    }

    // Menghapus aset
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(['message' => 'Aset berhasil dihapus!']);
    }

    // Ekspor ke CSV
    public function exportCsv(Request $request)
    {
        // Logika filter sama seperti di index
        $query = Asset::query();
        if ($request->filled('search_pic')) { $query->where('pic', 'like', '%' . $request->search_pic . '%'); }
        // ... (tambahkan filter lainnya jika perlu) ...
        $assets = $query->get();

        $fileName = 'laporan_aset.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Tipe', 'Jenis Aset', 'PIC', 'Merk', 'Nomor SN', 'Project', 'Lokasi', 'Tahun Beli', 'Harga Beli', 'Harga Sewa'];

        $callback = function() use($assets, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->id, $asset->tipe, $asset->jenis_aset, $asset->pic, $asset->merk, $asset->nomor_sn,
                    $asset->project, $asset->lokasi, $asset->tahun_beli, $asset->harga_beli, $asset->harga_sewa
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
