const db = require('../../config/db');

async function run() {
  const bhp = [
    [1, 1, 'Kabel LAN RJ45 Cat 6', 150, 20, 'meter', 'baik'],
    [2, 1, 'Konektor RJ45', 200, 50, 'pcs', 'baik'],
    [3, 3, 'Tinta Printer Epson L3110 Black', 10, 2, 'botol', 'baik'],
    [4, 4, 'Kertas HVS A4 80gr', 15, 3, 'rim', 'baik'],
    [5, 1, 'Kabel HDMI 1.5 Meter', 25, 5, 'pcs', 'baik'],
    [6, 1, 'Stop Kontak / Terminal Listrik 5 Lubang', 12, 3, 'pcs', 'baik'],
    [7, 1, 'Thermal Paste DeepCool Z5', 8, 2, 'pcs', 'baik'],
    [8, 2, 'Cable Ties 20cm Black', 5, 1, 'pack', 'baik']
  ];

  for (const b of bhp) {
    await db.query(
      `INSERT INTO bhp (id, ruangan_id, nama_bhp, stok, stok_minimum, satuan, kondisi) VALUES (?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE nama_bhp=VALUES(nama_bhp), stok=VALUES(stok), stok_minimum=VALUES(stok_minimum), satuan=VALUES(satuan), kondisi=VALUES(kondisi)`,
      b
    );
  }
  console.log('🌱  Seeded "bhp" table.');
}

module.exports = { run };
