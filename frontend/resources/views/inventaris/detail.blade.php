@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mt-4" style="border-radius: 12px;">
                <div class="card-header bg-light border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="ti ti-info-circle text-primary me-2"></i>Detail Barang Inventaris
                    </h5>
                    @if(Session::has('user'))
                    <a href="{{ route('inventaris') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Info Column -->
                        <div class="col-md-7">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Informasi Barang</h6>
                            <table class="table table-borderless table-sm small">
                                <tr>
                                    <th width="35%" class="text-muted fw-normal">Nama Barang</th>
                                    <td width="5%" class="text-muted">:</td>
                                    <td class="fw-semibold text-dark">{{ $item['nama_barang'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Kode / Label</th>
                                    <td class="text-muted">:</td>
                                    <td><span class="badge bg-primary fs-7">{{ $item['nomor_label'] ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Kategori</th>
                                    <td class="text-muted">:</td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1">{{ strtoupper($item['kategori'] ?? 'Lainnya') }}</span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Jenis</th>
                                    <td class="text-muted">:</td>
                                    <td>{{ $item['jenis'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Ruangan</th>
                                    <td class="text-muted">:</td>
                                    <td class="fw-medium">{{ $item['nama_ruangan'] ?? 'Belum Ditempatkan' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Tanggal Terima</th>
                                    <td class="text-muted">:</td>
                                    <td>{{ $item['tanggal_terima'] ? \Carbon\Carbon::parse($item['tanggal_terima'])->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Kondisi</th>
                                    <td class="text-muted">:</td>
                                    <td>
                                        @php
                                            $kondisi = $item['kondisi'] ?? 'baik';
                                            $badgeClass = $kondisi === 'baik' ? 'bg-success' : ($kondisi === 'rusak_ringan' ? 'bg-warning text-dark' : ($kondisi === 'rusak_berat' ? 'bg-danger' : 'bg-secondary'));
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ str_replace('_', ' ', strtoupper($kondisi)) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- QR Code Column -->
                        <div class="col-md-5 border-start-md">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">QR Code & Labeling</h6>
                            <div class="d-flex flex-column gap-4 align-items-center">
                                <!-- QR Inventaris -->
                                <div class="text-center w-100 p-2 bg-light rounded border">
                                    <span class="d-block small fw-bold text-muted mb-2">QR INVENTARIS LAB</span>
                                    <div class="mb-2">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url('/inventaris/detail/' . $item['id'])) }}" 
                                             alt="QR Local" 
                                             class="img-thumbnail" 
                                             style="width: 120px; height: 120px;">
                                    </div>
                                    <span class="small font-monospace text-dark d-block fw-semibold">{{ $item['nomor_label'] ?? '' }}</span>
                                </div>
                                <!-- QR Universitas -->
                                <div class="text-center w-100 p-2 bg-light rounded border">
                                    <span class="d-block small fw-bold text-muted mb-2">QR UNIVERSITAS</span>
                                    @if(!empty($item['qr_univ_path']))
                                    <img src="{{ $item['qr_univ_path'] }}" 
                                         alt="QR Universitas" 
                                         class="img-thumbnail" 
                                         style="width: 120px; height: 120px; object-fit: contain;">
                                    <a href="{{ $item['qr_univ_path'] }}" target="_blank" class="d-block btn btn-link btn-sm mt-1 p-0 text-decoration-none">
                                        <i class="ti ti-external-link"></i> Lihat Penuh
                                    </a>
                                    @else
                                    <div class="text-muted py-4 small bg-white border rounded">
                                        <i class="ti ti-qrcode fs-3 d-block mb-1 opacity-50"></i>
                                        Belum diunggah
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
