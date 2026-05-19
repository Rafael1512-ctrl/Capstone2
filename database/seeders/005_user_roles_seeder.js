const db = require('../../config/db');

async function run() {
  const userRoles = [
    [1, 1, 1], // Admin Utama -> admin
    [2, 2, 2], // Kepala Lab -> kepala_lab
    [3, 3, 3], // Ketua Prodi -> ketua_prodi
    [4, 4, 4], // Staf Admin -> staf_admin
    [5, 5, 5]  // Staf Lab -> staf_lab
  ];

  for (const ur of userRoles) {
    await db.query(
      `INSERT INTO user_roles (id, user_id, role_id) VALUES (?, ?, ?)
       ON DUPLICATE KEY UPDATE user_id=VALUES(user_id), role_id=VALUES(role_id)`,
      ur
    );
  }
  console.log('🌱  Seeded "user_roles" table.');
}

module.exports = { run };
