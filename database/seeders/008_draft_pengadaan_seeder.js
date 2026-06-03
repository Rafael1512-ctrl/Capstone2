const db = require('../../config/db');

async function run() {
  const drafts = [
    [1, 2, 3, 2026, 'finalized', null],
    [2, 2, null, 2026, 'draft', null],
    [3, 2, 3, 2026, 'finalized', null],
    [4, 2, 3, 2026, 'rejected', 'Anggaran tidak mencukupi dan barang tidak menunjang langsung kurikulum lab IT.'],
    [5, 2, 3, 2026, 'finalized', null],
    [6, 2, 3, 2026, 'submitted', null],
    [7, 2, 3, 2026, 'reviewed', null]
  ];

  for (const d of drafts) {
    await db.query(
      `INSERT INTO draft_pengadaan (id, user_id, ketua_prodi_id, tahun, status, alasan_penolakan) VALUES (?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE user_id=VALUES(user_id), ketua_prodi_id=VALUES(ketua_prodi_id), tahun=VALUES(tahun), status=VALUES(status), alasan_penolakan=VALUES(alasan_penolakan)`,
      d
    );
  }
  console.log('🌱  Seeded "draft_pengadaan" table with diverse scenarios.');
}

module.exports = { run };
