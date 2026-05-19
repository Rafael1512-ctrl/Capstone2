const db = require('../../config/db');

async function run() {
  const bhp = [
    [1, 1, 'Kabel LAN RJ45 Cat 6', 150, 20, 'meter'],
    [2, 1, 'Konektor RJ45', 200, 50, 'pcs'],
    [3, 3, 'Tinta Printer Epson L3110 Black', 10, 2, 'botol'],
    [4, 4, 'Kertas HVS A4 80gr', 15, 3, 'rim']
  ];

  for (const b of bhp) {
    await db.query(
      `INSERT INTO bhp (id, ruangan_id, nama_bhp, stok_saat_ini, stok_minimum, satuan) VALUES (?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE nama_bhp=VALUES(nama_bhp), stok_saat_ini=VALUES(stok_saat_ini), stok_minimum=VALUES(stok_minimum), satuan=VALUES(satuan)`,
      b
    );
  }
  console.log('🌱  Seeded "bhp" table.');
}

module.exports = { run };
