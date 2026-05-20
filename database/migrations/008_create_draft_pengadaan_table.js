const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS draft_pengadaan (
      id int(11) NOT NULL AUTO_INCREMENT,
      user_id int(11) NOT NULL,
      ketua_prodi_id int(11) DEFAULT NULL COMMENT 'diisi saat review',
      tahun year(4) NOT NULL,
      status varchar(20) NOT NULL DEFAULT 'draft' COMMENT 'draft | locked | submitted | reviewed | finalized | rejected',
      alasan_penolakan text DEFAULT NULL,
      created_at timestamp NULL DEFAULT current_timestamp(),
      finalized_at timestamp NULL DEFAULT NULL,
      PRIMARY KEY (id),
      KEY user_id (user_id),
      KEY ketua_prodi_id (ketua_prodi_id),
      CONSTRAINT draft_pengadaan_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
      CONSTRAINT draft_pengadaan_ibfk_2 FOREIGN KEY (ketua_prodi_id) REFERENCES users (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "draft_pengadaan" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS draft_pengadaan`);
  console.log('🗑️  Table "draft_pengadaan" dropped.');
}

module.exports = { up, down };
