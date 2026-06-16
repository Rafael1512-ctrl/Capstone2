@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Staf Administrasi - Draf Pengadaan Disetujui</h1>
          <p class="text-muted">Tinjau daftar draf pengadaan barang tahunan yang telah lolos review dan disetujui (finalized) oleh Ketua Program Studi.</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Daftar Pengadaan Finalized</h5>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 table-hover">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">ID Draf</th>
                  <th>Tahun Anggaran</th>
                  <th>Pengaju (Kepala Lab)</th>
                  <th>Jumlah Barang Disetujui</th>
                  <th>Status Draf</th>
                  <th class="text-end px-4">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($drafts) && count($drafts) > 0)
                  @foreach ($drafts as $d)
                    <tr>
                      <td class="px-4 py-3">{{ $d['id'] }}</td>
                      <td class="fw-semibold">{{ $d['tahun'] }}</td>
                      <td>{{ $d['pengaju'] }}</td>
                      <td>{{ $d['approved_items'] }} items disetujui</td>
                      <td>
                        <span class="badge bg-success">FINALIZED</span>
                      </td>
                       <td class="text-end px-4">
                        <a class="btn btn-sm btn-primary" href="/inventaris?draft_id={{ $d['id'] }}">
                          <i class="ti ti-edit me-1"></i>Input Penerimaan & Labeling
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="6">Belum ada draf pengadaan berstatus finalized untuk diproses.</td>
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
