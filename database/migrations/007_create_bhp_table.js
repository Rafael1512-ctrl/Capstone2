const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS bhp (
      id int(11) NOT NULL AUTO_INCREMENT,
      ruangan_id int(11) NOT NULL,
      nama_bhp varchar(150) NOT NULL,
      stok int(11) NOT NULL DEFAULT 0,
      stok_minimum int(11) NOT NULL DEFAULT 0,
      satuan varchar(30) NOT NULL,
      kondisi varchar(20) NOT NULL DEFAULT 'baik',
      updated_at timestamp NULL DEFAULT current_timestamp(),
      PRIMARY KEY (id),
      KEY ruangan_id (ruangan_id),
      CONSTRAINT bhp_ibfk_1 FOREIGN KEY (ruangan_id) REFERENCES ruangan (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "bhp" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS bhp`);
  console.log('🗑️  Table "bhp" dropped.');
}

module.exports = { up, down };
