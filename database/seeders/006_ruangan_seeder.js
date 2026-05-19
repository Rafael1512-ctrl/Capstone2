const db = require('../../config/db');

async function run() {
  const ruangan = [
    [1, 'R-101', 'Laboratorium Komputer 1', 'Gedung A Lantai 2', 40],
    [2, 'R-102', 'Laboratorium Komputer 2', 'Gedung A Lantai 2', 30],
    [3, 'R-201', 'Laboratorium Hardware', 'Gedung B Lantai 1', 20],
    [4, 'R-001', 'Gudang Inventaris', 'Gedung B Lantai 1', 10]
  ];

  for (const r of ruangan) {
    await db.query(
      `INSERT INTO ruangan (id, kode_ruangan, nama_ruangan, lokasi, kapasitas) VALUES (?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE kode_ruangan=VALUES(kode_ruangan), nama_ruangan=VALUES(nama_ruangan), lokasi=VALUES(lokasi), kapasitas=VALUES(kapasitas)`,
      r
    );
  }
  console.log('🌱  Seeded "ruangan" table.');
}

module.exports = { run };
