const db = require('../../config/db');

async function run() {
  const roles = [
    [1, 'admin'],
    [2, 'kepala_lab'],
    [3, 'ketua_prodi'],
    [4, 'staf_admin'],
    [5, 'staf_lab']
  ];

  for (const role of roles) {
    await db.query(
      `INSERT INTO roles (id, nama) VALUES (?, ?)
       ON DUPLICATE KEY UPDATE nama=VALUES(nama)`,
      role
    );
  }
  console.log('🌱  Seeded "roles" table.');
}

module.exports = { run };
