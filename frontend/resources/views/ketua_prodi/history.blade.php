@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Ketua Prodi - Riwayat Draf Pengadaan</h1>
          <p class="text-muted">Lihat semua draf pengadaan tahunan dari Kepala Laboratorium yang telah Anda setujui (finalisasi) atau tolak.</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Riwayat Keputusan Draf</h5>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 table-hover">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">ID Draf</th>
                  <th>Tahun</th>
                  <th>Pengaju (Kepala Lab)</th>
                  <th>Total Items</th>
                  <th>Catatan Keputusan</th>
                  <th class="">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($drafts) && count($drafts) > 0)
                  @foreach ($drafts as $d)
                    <tr>
                      <td class="px-4 py-3">#{{ $d['id'] }}</td>
                      <td class="fw-semibold">{{ $d['tahun'] }}</td>
                      <td>{{ $d['pengaju'] }}</td>
                      <td>{{ $d['total_items'] }} items</td>
                      <td>
                        @if ($d['alasan_penolakan'])
                          <span class="text-muted small">{{ $d['alasan_penolakan'] }}</span>
                        @else
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                      <td class="text-end px-4">
                        <a class="btn btn-sm btn-outline-primary" href="/ketua-prodi/review/{{ $d['id'] }}">
                          <i class="ti ti-search me-1"></i>Detail
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="7">Tidak ada riwayat draf pengadaan.</td>
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
