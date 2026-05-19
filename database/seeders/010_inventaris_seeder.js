const db = require('../../config/db');

async function run() {
  const inventaris = [
    [1, 1, 1, 'PC Client Lenovo ThinkCentre', 'LAB1-PC-001', 'QR-LAB1-PC-001', 'baik', '2026-02-10'],
    [2, 1, 1, 'PC Client Lenovo ThinkCentre', 'LAB1-PC-002', 'QR-LAB1-PC-002', 'baik', '2026-02-10'],
    [3, 1, 2, 'Switch Hub Cisco 24 Port', 'LAB1-SW-001', 'QR-LAB1-SW-001', 'rusak_ringan', '2026-02-15']
  ];

  for (const inv of inventaris) {
    await db.query(
      `INSERT INTO inventaris (id, ruangan_id, detail_draft_id, nama_barang, nomor_label, barcode_qr, kondisi, tanggal_terima) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE nama_barang=VALUES(nama_barang), nomor_label=VALUES(nomor_label), barcode_qr=VALUES(barcode_qr), kondisi=VALUES(kondisi), tanggal_terima=VALUES(tanggal_terima)`,
      inv
    );
  }
  console.log('🌱  Seeded "inventaris" table.');
}

module.exports = { run };
