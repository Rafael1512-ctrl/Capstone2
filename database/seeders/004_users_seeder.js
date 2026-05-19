const db = require('../../config/db');

async function run() {
  // Password default 'password' dalam bcrypt
  const bcryptPassword = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
  const users = [
    [1, 'Admin Utama', 'admin@mail.com', bcryptPassword],
    [2, 'Kepala Lab', 'kepala@mail.com', bcryptPassword],
    [3, 'Ketua Prodi', 'prodi@mail.com', bcryptPassword],
    [4, 'Staf Admin', 'stafadmin@mail.com', bcryptPassword],
    [5, 'Staf Lab', 'staflab@mail.com', bcryptPassword]
  ];

  for (const user of users) {
    await db.query(
      `INSERT INTO users (id, nama, email, password) VALUES (?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE nama=VALUES(nama), password=VALUES(password)`,
      user
    );
  }
  console.log('🌱  Seeded "users" table.');
}

module.exports = { run };
