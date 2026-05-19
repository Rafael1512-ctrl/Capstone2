const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS ruangan (
      id int(11) NOT NULL AUTO_INCREMENT,
      kode_ruangan varchar(50) DEFAULT NULL,
      nama_ruangan varchar(100) NOT NULL,
      lokasi varchar(100) DEFAULT NULL,
      kapasitas int(11) DEFAULT NULL,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  
  try {
    const [cols] = await db.query(`SHOW COLUMNS FROM ruangan LIKE 'kode_ruangan'`);
    if (cols.length === 0) {
      await db.query(`ALTER TABLE ruangan ADD COLUMN kode_ruangan varchar(50) DEFAULT NULL AFTER id`);
      console.log('✅ Added "kode_ruangan" column to "ruangan" table.');
    }
  } catch (err) {
    console.error('Error adding column to ruangan:', err);
  }

  console.log('✅  Table "ruangan" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS ruangan`);
  console.log('🗑️  Table "ruangan" dropped.');
}

module.exports = { up, down };
