const db = require('../../config/db');

async function run() {
  const drafts = [
    [1, 2, 3, 2026, 'finalized'],
    [2, 2, null, 2026, 'draft']
  ];

  for (const d of drafts) {
    await db.query(
      `INSERT INTO draft_pengadaan (id, kepala_lab_id, ketua_prodi_id, tahun, status) VALUES (?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE kepala_lab_id=VALUES(kepala_lab_id), ketua_prodi_id=VALUES(ketua_prodi_id), tahun=VALUES(tahun), status=VALUES(status)`,
      d
    );
  }
  console.log('🌱  Seeded "draft_pengadaan" table.');
}

module.exports = { run };
