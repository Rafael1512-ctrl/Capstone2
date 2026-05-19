const db = require('../../config/db');

async function run() {
  const maintenanceLogs = [
    [1, 3, 5, '2026-04-10', 'Perbaikan port switch cisco yang mati', 'rusak_ringan', 'baik', 2, 5]
  ];

  for (const m of maintenanceLogs) {
    await db.query(
      `INSERT INTO maintenance_log (id, inventaris_id, staf_lab_id, tanggal_maintenance, deskripsi, kondisi_sebelum, kondisi_sesudah, bhp_digunakan_id, jumlah_bhp_digunakan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE deskripsi=VALUES(deskripsi), kondisi_sebelum=VALUES(kondisi_sebelum), kondisi_sesudah=VALUES(kondisi_sesudah), jumlah_bhp_digunakan=VALUES(jumlah_bhp_digunakan)`,
      m
    );
  }
  console.log('🌱  Seeded "maintenance_log" table.');
}

module.exports = { run };
