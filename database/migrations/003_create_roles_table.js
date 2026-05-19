const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS roles (
      id int(11) NOT NULL AUTO_INCREMENT,
      nama varchar(50) NOT NULL COMMENT 'admin | kepala_lab | ketua_prodi | staf_admin | staf_lab',
      PRIMARY KEY (id),
      UNIQUE KEY nama (nama)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "roles" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS roles`);
  console.log('🗑️  Table "roles" dropped.');
}

module.exports = { up, down };
