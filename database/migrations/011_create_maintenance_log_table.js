const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS maintenance_log (
      id int(11) NOT NULL AUTO_INCREMENT,
      inventaris_id int(11) NOT NULL,
      staf_lab_id int(11) NOT NULL,
      tanggal_maintenance date NOT NULL,
      deskripsi text NOT NULL,
      kondisi_sebelum varchar(20) NOT NULL,
      kondisi_sesudah varchar(20) NOT NULL,
      bhp_digunakan_id int(11) DEFAULT NULL,
      jumlah_bhp_digunakan int(11) DEFAULT NULL,
      created_at timestamp NULL DEFAULT current_timestamp(),
      PRIMARY KEY (id),
      KEY inventaris_id (inventaris_id),
      KEY staf_lab_id (staf_lab_id),
      KEY bhp_digunakan_id (bhp_digunakan_id),
      CONSTRAINT maintenance_log_ibfk_1 FOREIGN KEY (inventaris_id) REFERENCES inventaris (id) ON DELETE CASCADE,
      CONSTRAINT maintenance_log_ibfk_2 FOREIGN KEY (staf_lab_id) REFERENCES users (id) ON DELETE CASCADE,
      CONSTRAINT maintenance_log_ibfk_3 FOREIGN KEY (bhp_digunakan_id) REFERENCES bhp (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "maintenance_log" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS maintenance_log`);
  console.log('🗑️  Table "maintenance_log" dropped.');
}

module.exports = { up, down };
