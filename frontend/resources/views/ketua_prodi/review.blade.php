@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Ketua Prodi - Review Draf Pengadaan</h1>
          <p class="text-muted">Tinjau pengajuan draf pengadaan tahunan dari Kepala Laboratorium. Setujui atau tolak item barang secara mandiri sebelum melakukan finalisasi.</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Pengajuan Draf Menunggu Review</h5>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 table-hover">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">ID Draf</th>
                  <th>Tahun</th>
                  <th>Pengaju (Kepala Lab)</th>
                  <th>Total Items</th>
                  <th>Status Review</th>
                  <th>Status Draf</th>
                  <th class="text-end px-4">Aksi</th>
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
                        @if ($d['pending_items'] > 0)
                          <span class="text-danger fw-semibold">{{ $d['pending_items'] }} Pending Review</span>
                        @else
                          <span class="text-success fw-semibold">Semua Selesai Direview</span>
                        @endif
                      </td>
                      <td>
                        @php
                          $badgeClass = $d['status'] === 'submitted' ? 'bg-info' : ($d['status'] === 'reviewed' ? 'bg-primary' : ($d['status'] === 'finalized' ? 'bg-success' : 'bg-secondary'));
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                          {{ $d['status'] === 'finalized' ? 'APPROVED' : strtoupper($d['status']) }}
                        </span>
                      </td>
                      <td class="text-end px-4">
                        <a class="btn btn-sm btn-primary" href="/ketua-prodi/review/{{ $d['id'] }}">
                          <i class="ti ti-search me-1"></i>Tinjau Detail
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="7">Tidak ada draf yang perlu ditinjau.</td>
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
