@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Staf Administrasi - Penerimaan & Labeling Inventaris</h1>
          <p class="text-muted">Lakukan pencatatan penerimaan barang, penomoran label/kode inventaris, pemetaan ruangan, dan generate QR Code untuk barang inventaris yang telah disetujui.</p>
        </div>
      </div>
    </div>

    <!-- PENDING ITEMS SECTION -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-warning bg-opacity-10 border-bottom px-4 py-3 border-warning border-opacity-25">
            <h5 class="mb-0 text-warning-emphasis">Menunggu Penerimaan & Penomoran Label (Barang Approved)</h5>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">Barang (Pengadaan)</th>
                  <th>Tahun</th>
                  <th>Qty</th>
                  <th>Form Labeling & Penerimaan</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($pendingItems) && count($pendingItems) > 0)
                  @foreach ($pendingItems as $item)
                    <tr>
                      <td class="px-4 py-3">
                        <div class="fw-semibold">{{ $item['nama_barang'] }}</div>
                        <small class="text-muted">Diajukan oleh: {{ $item['pengaju'] }}</small>
                      </td>
                      <td>{{ $item['tahun'] }}</td>
                      <td>{{ $item['jumlah'] }}</td>
                      <td>
                        <form class="row g-2 align-items-end py-2" action="/staf-admin/inventaris/receive/{{ $item['id'] }}" method="POST">
                          @csrf
                          <div class="col-md-3 col-12">
                            <label class="form-label small mb-1">Kode Inventaris / Label</label>
                            <input class="form-control form-control-sm" type="text" name="nomor_label" placeholder="cth. INV-LAB-01" required>
                          </div>
                          <div class="col-md-3 col-12">
                            <label class="form-label small mb-1">Ruangan Penempatan</label>
                            <select class="form-select form-select-sm" name="ruangan_id" required>
                              @foreach ($ruangan as $r)
                                <option value="{{ $r['id'] }}">{{ $r['kode_ruangan'] }} - {{ $r['nama_ruangan'] }}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-md-3 col-12">
                            <label class="form-label small mb-1">Tgl Penerimaan</label>
                            <input class="form-control form-control-sm" type="date" name="tanggal_terima" required>
                          </div>
                          <div class="col-md-2 col-12">
                            <label class="form-label small mb-1">Kondisi Awal</label>
                            <select class="form-select form-select-sm" name="kondisi">
                              <option value="baik">Baik</option>
                              <option value="perlu_perbaikan">Perlu Perbaikan</option>
                              <option value="rusak">Rusak</option>
                            </select>
                          </div>
                          <div class="col-md-1 col-12 d-grid">
                            <button class="btn btn-sm btn-primary" type="submit">
                              <i class="ti ti-check"></i>
                            </button>
                          </div>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4 text-muted" colspan="4">Tidak ada barang inventaris pending untuk dilabeli.</td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- RECEIVED ITEMS SECTION -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Inventaris Terdaftar & QR-Labeled</h5>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 table-hover">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">ID</th>
                  <th>Kode/Label</th>
                  <th>Nama Barang</th>
                  <th>Ruangan</th>
                  <th>Tanggal Terima</th>
                  <th>Kondisi</th>
                  <th class="text-end px-4">QR Code / Barcode</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($receivedItems) && count($receivedItems) > 0)
                  @foreach ($receivedItems as $inv)
                    <tr>
                      <td class="px-4 py-3">{{ $inv['id'] }}</td>
                      <td><span class="badge bg-primary fs-7">{{ $inv['kode_inventaris'] }}</span></td>
                      <td class="fw-semibold">{{ $inv['nama_barang'] }}</td>
                      <td>{{ $inv['nama_ruangan'] ?? 'Belum Ditempatkan' }}</td>
                      <td>{{ \Carbon\Carbon::parse($inv['tanggal_penerimaan'])->translatedFormat('d M Y') }}</td>
                      <td>
                        @php
                          $badgeClass = $inv['status'] === 'baik' ? 'bg-success' : ($inv['status'] === 'perlu_perbaikan' ? 'bg-warning' : 'bg-danger');
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                          {{ strtoupper($inv['status']) }}
                        </span>
                      </td>
                      <td class="text-end px-4">
                        <div class="d-inline-flex align-items-center gap-2">
                          <i class="ti ti-qrcode fs-3 text-secondary"></i>
                          <span class="small text-muted font-monospace">{{ $inv['foto_qr'] }}</span>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="7">Belum ada barang inventaris yang dilabeli.</td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
