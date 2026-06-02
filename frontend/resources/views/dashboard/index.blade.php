@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6 d-flex justify-content-between align-items-center">
          <div>
            <h1 class="fs-3 mb-1">Dashboard</h1>
            <p class="text-muted">Selamat datang! Berikut ringkasan performa dan inventori laboratorium Anda.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- LOW STOCK BHP WARNINGS -->
    @if (isset($lowStockBhp['count']) && $lowStockBhp['count'] > 0)
      <div class="alert alert-warning border-start border-4 border-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
        <i class="ti ti-alert-triangle fs-3 me-3 text-warning"></i>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="alert-heading mb-1 fw-bold text-dark">Peringatan Batas Minimum Stok BHP!</h5>
            @if (Session::has('user') && Session::get('user')['role'] === 'staf_lab')
              <a href="/staf-lab/bhp" class="btn btn-sm btn-warning fw-semibold px-3 py-1">Kelola BHP</a>
            @endif
          </div>
          <p class="mb-0 text-dark small">Terdapat <strong>{{ $lowStockBhp['count'] }}</strong> item barang habis pakai (BHP) yang stoknya berada di bawah batas minimum yang ditentukan.</p>
        </div>
      </div>
    @endif

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-primary text-white rounded-2">
              <i class="ti ti-cash fs-4"></i>
            </div>
            <div>
              <h2 class="mb-2 fs-6 text-muted">Anggaran Disetujui</h2>
              <h3 class="fw-bold mb-0 text-dark">Rp. {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</h3>
              <p class="text-primary mb-0 small">Total biaya pengadaan</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-success text-white rounded-2">
              <i class="ti ti-box-seam fs-4"></i>
            </div>
            <div>
              <h2 class="mb-2 fs-6 text-muted">Total Aset Aktif</h2>
              <h3 class="fw-bold mb-0 text-dark">{{ $totalAset ?? 0 }} Aset</h3>
              <p class="text-success mb-0 small">Barang dalam kondisi baik/rusak</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-warning text-white rounded-2">
              <i class="ti ti-alert-triangle fs-4"></i>
            </div>
            <div>
              <h2 class="mb-2 fs-6 text-muted">Stok BHP Kritis</h2>
              <h3 class="fw-bold mb-0 text-dark">{{ $lowStock ?? 0 }} Item</h3>
              <p class="text-warning mb-0 small">Stok &le; batas minimum</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card p-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-danger text-white rounded-2">
              <i class="ti ti-package-off fs-4"></i>
            </div>
            <div>
              <h2 class="mb-2 fs-6 text-muted">BHP Habis</h2>
              <h3 class="fw-bold mb-0 text-dark">{{ $outOfStock ?? 0 }} Item</h3>
              <p class="text-danger mb-0 small">Jumlah stok = 0 unit</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
      <!-- Chart 1: Pengeluaran Pengadaan Tahunan -->
      <div class="col-12 col-lg-7">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h3 class="h5 mb-0 fw-semibold text-dark">Grafik Anggaran Pengadaan Tahunan</h3>
            <span class="badge bg-secondary-subtle text-secondary">Berdasarkan Tahun Draf</span>
          </div>
          <div class="card-body p-4">
            <div id="salesPurchaseChart"></div>
          </div>
        </div>
      </div>
      <!-- Chart 2: Kondisi Aset -->
      <div class="col-12 col-lg-5">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h3 class="h5 mb-0 fw-semibold text-dark">Proporsi Kondisi Aset</h3>
          </div>
          <div class="card-body p-4">
            <div class="row align-items-center">
              <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                <div id="customerChart" class="d-flex justify-content-center"></div>
              </div>
              <div class="col-12 col-sm-6">
                <div class="row g-2">
                  <div class="col-12 border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="text-secondary small d-flex align-items-center">
                        <span class="d-inline-block rounded-circle bg-success me-2" style="width: 8px; height: 8px;"></span>Baik
                      </span>
                      <strong class="text-success">{{ $chartData['conditions']['baik'] ?? 0 }}</strong>
                    </div>
                  </div>
                  <div class="col-12 border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="text-secondary small d-flex align-items-center">
                        <span class="d-inline-block rounded-circle bg-warning me-2" style="width: 8px; height: 8px;"></span>Rusak Ringan
                      </span>
                      <strong class="text-warning">{{ $chartData['conditions']['rusak_ringan'] ?? 0 }}</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="text-secondary small d-flex align-items-center">
                        <span class="d-inline-block rounded-circle bg-danger me-2" style="width: 8px; height: 8px;"></span>Rusak Berat
                      </span>
                      <strong class="text-danger">{{ $chartData['conditions']['rusak_berat'] ?? 0 }}</strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row text-center border-top mt-4 pt-4 g-1">
              <div class="col-4 border-end">
                <h4 class="fw-bold text-dark mb-1">{{ $totalAset ?? 0 }}</h4>
                <small class="text-secondary">Aset</small>
              </div>
              <div class="col-4 border-end">
                <h4 class="fw-bold text-dark mb-1">{{ $totalBhp ?? 0 }}</h4>
                <small class="text-secondary">Variasi BHP</small>
              </div>
              <div class="col-4">
                <h4 class="fw-bold text-dark mb-1">{{ $totalMaintenance ?? 0 }}</h4>
                <small class="text-secondary">Log Rawat</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Lists Section -->
    <div class="row g-3">
      <!-- List 1: Low Stock BHP Items -->
      <div class="col-lg-4 col-12">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-transparent px-4 py-3 d-flex justify-content-between align-items-center border-bottom">
            <h4 class="mb-0 h5 fw-semibold text-dark">Stok BHP Kritis / Rendah</h4>
            @if (Session::has('user') && Session::get('user')['role'] === 'staf_lab')
              <a class="small text-primary text-decoration-underline" href="/staf-lab/bhp">Detail</a>
            @endif
          </div>
          <ul class="list-group list-group-flush">
            @if (isset($lowStockBhp['items']) && count($lowStockBhp['items']) > 0)
              @foreach (array_slice($lowStockBhp['items'], 0, 5) as $bhp)
                <li class="list-group-item d-flex align-items-center gap-3 py-3">
                  <div class="bg-warning-subtle text-warning rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="ti ti-alert-triangle fs-4"></i>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-0 fw-semibold text-dark small">{{ $bhp['nama_bhp'] }}</p>
                    <small class="text-secondary">{{ $bhp['nama_ruangan'] ?? 'Gudang Utama' }}</small>
                  </div>
                  <div class="text-end">
                    <span class="badge bg-danger">{{ $bhp['stok'] }} / {{ $bhp['stok_minimum'] }}</span>
                    <small class="d-block text-secondary" style="font-size: 0.75rem;">{{ $bhp['satuan'] }}</small>
                  </div>
                </li>
              @endforeach
            @else
              <li class="list-group-item text-muted text-center py-4">Semua stok BHP berada dalam kondisi aman.</li>
            @endif
          </ul>
        </div>
      </div>

      <!-- List 2: Recent Assets Received -->
      <div class="col-lg-4 col-12">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-transparent px-4 py-3 d-flex justify-content-between align-items-center border-bottom">
            <h4 class="mb-0 h5 fw-semibold text-dark">Penerimaan Aset Terbaru</h4>
            @if (Session::has('user') && Session::get('user')['role'] === 'staf_admin')
              <a class="small text-primary text-decoration-underline" href="/staf-admin/inventaris">Kelola</a>
            @endif
          </div>
          <ul class="list-group list-group-flush">
            @if (isset($recentAssets) && count($recentAssets) > 0)
              @foreach ($recentAssets as $asset)
                <li class="list-group-item d-flex align-items-center gap-3 py-3">
                  <div class="bg-success-subtle text-success rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="ti ti-barcode fs-4"></i>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-0 fw-semibold text-dark small">{{ $asset['nama_barang'] }}</p>
                    <small class="text-secondary font-monospace">{{ $asset['nomor_label'] }}</small>
                  </div>
                  <div class="text-end">
                    <span class="badge bg-success-subtle text-success text-capitalize small">{{ str_replace('_', ' ', $asset['kondisi']) }}</span>
                    <small class="d-block text-secondary" style="font-size: 0.75rem;">{{ date('d/m/Y', strtotime($asset['tanggal_terima'])) }}</small>
                  </div>
                </li>
              @endforeach
            @else
              <li class="list-group-item text-muted text-center py-4">Belum ada aset baru yang diterima.</li>
            @endif
          </ul>
        </div>
      </div>

      <!-- List 3: Recent Maintenance Activities -->
      <div class="col-lg-4 col-12">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-transparent px-4 py-3 d-flex justify-content-between align-items-center border-bottom">
            <h4 class="mb-0 h5 fw-semibold text-dark">Log Perawatan Aset Terbaru</h4>
            @if (Session::has('user') && Session::get('user')['role'] === 'staf_lab')
              <a class="small text-primary text-decoration-underline" href="/staf-lab/maintenance">Semua Log</a>
            @endif
          </div>
          <ul class="list-group list-group-flush">
            @if (isset($recentMaintenance) && count($recentMaintenance) > 0)
              @foreach ($recentMaintenance as $maint)
                <li class="list-group-item d-flex align-items-start gap-3 py-3">
                  <div class="bg-info-subtle text-info rounded p-2 d-flex align-items-center justify-content-center mt-1" style="width: 40px; height: 40px;">
                    <i class="ti ti-tool fs-4"></i>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-0 fw-semibold text-dark small">{{ $maint['nama_aset'] }}</p>
                    <small class="text-secondary font-monospace d-block">{{ $maint['nomor_label'] }}</small>
                    <span class="text-secondary small text-truncate d-inline-block" style="max-width: 180px;">{{ $maint['deskripsi'] }}</span>
                  </div>
                  <div class="text-end flex-shrink-0">
                    <span class="badge {{ $maint['kondisi_sesudah'] === 'baik' ? 'bg-success' : ($maint['kondisi_sesudah'] === 'perlu_perbaikan' ? 'bg-warning' : 'bg-danger') }} text-capitalize small">
                      &rarr; {{ str_replace('_', ' ', $maint['kondisi_sesudah']) }}
                    </span>
                    <small class="d-block text-secondary mt-1" style="font-size: 0.75rem;">{{ date('d/m/y', strtotime($maint['tanggal_maintenance'])) }}</small>
                  </div>
                </li>
              @endforeach
            @else
              <li class="list-group-item text-muted text-center py-4">Belum ada aktivitas pemeliharaan dicatat.</li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Global Javascript Object to Bridge Backend Data to Frontend ApexCharts Module -->
  <script>
    window.dashboardChartData = {
      conditions: {
        labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
        values: [
          {{ $chartData['conditions']['baik'] ?? 0 }},
          {{ $chartData['conditions']['rusak_ringan'] ?? 0 }},
          {{ $chartData['conditions']['rusak_berat'] ?? 0 }}
        ]
      },
      procurementExpenses: {
        years: {!! json_encode($chartData['expenses']['years'] ?? []) !!},
        values: {!! json_encode($chartData['expenses']['values'] ?? []) !!}
      }
    };
  </script>
@endsection
