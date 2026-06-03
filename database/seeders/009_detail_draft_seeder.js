const db = require('../../config/db');

async function run() {
  const details = [
    // Draft 1 (Finalized - Processed into inventory)
    [1, 1, 'PC Client Lenovo ThinkCentre', 'inventaris', 10, 7500000.00, 'https://tokopedia.com', '/assets/images/lenovo.png', 'approved', null],
    [2, 1, 'Switch Hub Cisco 24 Port', 'inventaris', 2, 3500000.00, 'https://tokopedia.com', '/assets/images/cisco.png', 'approved', null],
    [4, 1, 'Monitor ASUS VZ249HE 24 inch', 'inventaris', 10, 1650000.00, 'https://tokopedia.com', null, 'approved', null],
    [5, 1, 'Keyboard Logitech K120 USB', 'inventaris', 15, 110000.00, 'https://tokopedia.com', null, 'approved', null],
    
    // Draft 2 (Active Draft - Kopf / Kepala Lab)
    [3, 2, 'Mouse Logitech Wireless B170', 'inventaris', 20, 120000.00, 'https://tokopedia.com', null, 'pending', null],
    [6, 2, 'Kabel LAN Belden Cat6 1 Roll', 'bhp', 1, 1800000.00, 'https://tokopedia.com', null, 'pending', null],

    // Draft 3 (Finalized - Fully Approved / Disetujui Semua)
    [7, 3, 'Router Mikrotik RB951Ui-2HnD', 'inventaris', 3, 950000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk praktikum Komputasi Jaringan'],
    [8, 3, 'Access Point TP-Link EAP225', 'inventaris', 4, 1150000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk peningkatan sinyal Lab IT'],
    [9, 3, 'Thermal Paste DeepCool Z5', 'bhp', 10, 75000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk stok maintenance tahunan'],

    // Draft 4 (Rejected - Fully Rejected / Ditolak Semua)
    [10, 4, 'Gaming Chair DXRacer', 'inventaris', 5, 4200000.00, 'https://tokopedia.com', null, 'rejected', 'Bukan prioritas utama laboratorium akademik'],
    [11, 4, 'PlayStation 5 Console', 'inventaris', 2, 8500000.00, 'https://tokopedia.com', null, 'rejected', 'Tidak relevan dengan kurikulum program studi IT'],

    // Draft 5 (Finalized - Mixed: Approved and Rejected)
    [12, 5, 'SSD Samsung 870 EVO 500GB', 'inventaris', 10, 1100000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk upgrade PC Lab lama'],
    [13, 5, 'RAM DDR4 Corsair 8GB', 'inventaris', 16, 550000.00, 'https://tokopedia.com', null, 'approved', 'Disetujui untuk upgrade RAM PC Client'],
    [14, 5, 'HDMI Splitter 4 Port', 'inventaris', 3, 350000.00, 'https://tokopedia.com', null, 'rejected', 'Stok pembagi HDMI di gudang masih ada 5 unit yang baik'],
    [15, 5, 'Meja Lab Kayu Jati', 'inventaris', 8, 2500000.00, 'https://tokopedia.com', null, 'rejected', 'Pengajuan meja laboratorium harus melalui sarpras'],

    // Draft 6 (Submitted - Waiting for Prodi Review)
    [16, 6, 'Projector Epson EB-X400', 'inventaris', 2, 6200000.00, 'https://tokopedia.com', null, 'pending', null],
    [17, 6, 'Laser Presenter Logitech R400', 'inventaris', 5, 375000.00, 'https://tokopedia.com', null, 'pending', null],
    [18, 6, 'Kabel HDMI 10 Meter', 'bhp', 6, 250000.00, 'https://tokopedia.com', null, 'pending', null],

    // Draft 7 (Reviewed - Partial Review / Mixed with Pending)
    [19, 7, 'Monitor LG IPS 24 inch', 'inventaris', 5, 1900000.00, 'https://tokopedia.com', null, 'approved', 'Sangat dibutuhkan untuk asisten lab'],
    [20, 7, 'Drawing Tablet Wacom Intuos', 'inventaris', 3, 1500000.00, 'https://tokopedia.com', null, 'rejected', 'Praktikum desain grafis belum berjalan semester ini'],
    [21, 7, 'Webcam Logitech C922', 'inventaris', 4, 1450000.00, 'https://tokopedia.com', null, 'pending', null]
  ];

  for (const dt of details) {
    await db.query(
      `INSERT INTO detail_draft (id, draft_id, nama_barang, tipe_barang, jumlah, harga_satuan, link_pembelian, foto_url, status_item, catatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE draft_id=VALUES(draft_id), nama_barang=VALUES(nama_barang), tipe_barang=VALUES(tipe_barang), jumlah=VALUES(jumlah), harga_satuan=VALUES(harga_satuan), link_pembelian=VALUES(link_pembelian), foto_url=VALUES(foto_url), status_item=VALUES(status_item), catatan=VALUES(catatan)`,
      dt
    );
  }
  console.log('🌱  Seeded "detail_draft" table with all scenarios.');
}

module.exports = { run };
