const db = require('../../config/db');

async function run() {
  const details = [
    // Draft 1 (Finalized - Processed into inventory)
    // [id, draft_id, nama_barang, kategori, jenis, tipe_barang, jumlah, harga_satuan, link_pembelian, foto_url, status_item, catatan]
    [1, 1, 'PC Client Lenovo ThinkCentre', 'PC / Komputer', 'ThinkCentre M70q', 'inventaris', 10, 7500000.00, 'https://tokopedia.com', '/assets/images/lenovo.png', 'approved', null],
    [2, 1, 'Switch Hub Cisco 24 Port', 'Alat Jaringan', 'Catalyst 2960L', 'inventaris', 2, 3500000.00, 'https://tokopedia.com', '/assets/images/cisco.png', 'approved', null],
    [4, 1, 'Monitor ASUS VZ249HE 24 inch', 'Monitor', 'VZ249HE IPS', 'inventaris', 10, 1650000.00, 'https://tokopedia.com', null, 'approved', null],
    [5, 1, 'Keyboard Logitech K120 USB', 'Keyboard', 'Logitech K120 Wired', 'inventaris', 15, 110000.00, 'https://tokopedia.com', null, 'approved', null],
    
    // Draft 2 (Active Draft - Kopf / Kepala Lab)
    [3, 2, 'Mouse Logitech Wireless B170', 'Mouse', 'B170 Wireless', 'inventaris', 20, 120000.00, 'https://tokopedia.com', null, 'pending', null],
    [6, 2, 'Kabel LAN Belden Cat6 1 Roll', 'Kabel', 'LAN Cat6 1 Roll', 'bhp', 1, 1800000.00, 'https://tokopedia.com', null, 'pending', null],

    // Draft 3 (Finalized - Fully Approved / Disetujui Semua)
    [7, 3, 'Router Mikrotik RB951Ui-2HnD', 'Alat Jaringan', 'RB951Ui-2HnD', 'inventaris', 3, 950000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk praktikum Komputasi Jaringan'],
    [8, 3, 'Access Point TP-Link EAP225', 'Alat Jaringan', 'EAP225 AC1200', 'inventaris', 4, 1150000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk peningkatan sinyal Lab IT'],
    [9, 3, 'Thermal Paste DeepCool Z5', 'Lainnya', 'DeepCool Z5 3g', 'bhp', 10, 75000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk stok maintenance tahunan'],

    // Draft 4 (Rejected - Fully Rejected / Ditolak Semua)
    [10, 4, 'Gaming Chair DXRacer', 'Kursi', 'DXRacer Prince Series', 'inventaris', 5, 4200000.00, 'https://tokopedia.com', null, 'rejected', 'Bukan prioritas utama laboratorium akademik'],
    [11, 4, 'PlayStation 5 Console', 'Lainnya', 'PS5 Slim 1TB', 'inventaris', 2, 8500000.00, 'https://tokopedia.com', null, 'rejected', 'Tidak relevan dengan kurikulum program studi IT'],

    // Draft 5 (Finalized - Mixed: Approved and Rejected)
    [12, 5, 'SSD Samsung 870 EVO 500GB', 'Komponen PC', 'Samsung 870 EVO 500GB SATA', 'inventaris', 10, 1100000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk upgrade PC Lab lama'],
    [13, 5, 'RAM DDR4 Corsair 8GB', 'Komponen PC', 'Corsair Vengeance LPX DDR4 8GB', 'inventaris', 16, 550000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk upgrade RAM PC Client'],
    [14, 5, 'HDMI Splitter 4 Port', 'Lainnya', 'Splitter HDMI 1x4 4K', 'inventaris', 3, 350000.00, 'https://tokopedia.com', null, 'rejected', 'Stok pembagi HDMI di gudang masih ada 5 unit yang baik'],
    [15, 5, 'Meja Lab Kayu Jati', 'Meja', 'Meja Praktikum Kayu Jati', 'inventaris', 8, 2500000.00, 'https://tokopedia.com', null, 'rejected', 'Pengajuan meja laboratorium harus melalui sarpras'],

    // Draft 6 (Submitted - Waiting for Prodi Review)
    [16, 6, 'Projector Epson EB-X400', 'Lainnya', 'Epson EB-X400 XGA', 'inventaris', 2, 6200000.00, 'https://tokopedia.com', null, 'pending', null],
    [17, 6, 'Laser Presenter Logitech R400', 'Lainnya', 'Logitech R400 Wireless', 'inventaris', 5, 375000.00, 'https://tokopedia.com', null, 'pending', null],
    [18, 6, 'Kabel HDMI 10 Meter', 'Kabel', 'HDMI to HDMI 10M', 'bhp', 6, 250000.00, 'https://tokopedia.com', null, 'pending', null],

    // Draft 7 (Reviewed - Partial Review / Mixed with Pending)
    [19, 7, 'Monitor LG IPS 24 inch', 'Monitor', 'LG 24MK600', 'inventaris', 5, 1900000.00, 'https://tokopedia.com', null, 'approved', 'Sangat dibutuhkan untuk asisten lab'],
    [20, 7, 'Drawing Tablet Wacom Intuos', 'Lainnya', 'Wacom Intuos S Bluetooth', 'inventaris', 3, 1500000.00, 'https://tokopedia.com', null, 'rejected', 'Praktikum desain grafis belum berjalan semester ini'],
    [21, 7, 'Webcam Logitech C922', 'Lainnya', 'Logitech C922 Pro Stream', 'inventaris', 4, 1450000.00, 'https://tokopedia.com', null, 'pending', null]
  ];

  for (const dt of details) {
    await db.query(
      `INSERT INTO detail_draft (id, draft_id, nama_barang, kategori, jenis, tipe_barang, jumlah, harga_satuan, link_pembelian, foto_url, status_item, catatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE draft_id=VALUES(draft_id), nama_barang=VALUES(nama_barang), kategori=VALUES(kategori), jenis=VALUES(jenis), tipe_barang=VALUES(tipe_barang), jumlah=VALUES(jumlah), harga_satuan=VALUES(harga_satuan), link_pembelian=VALUES(link_pembelian), foto_url=VALUES(foto_url), status_item=VALUES(status_item), catatan=VALUES(catatan)`,
      dt
    );
  }
  console.log('🌱 Seeded "detail_draft" table with all scenarios including Kategori & Jenis.');
}

module.exports = { run };
