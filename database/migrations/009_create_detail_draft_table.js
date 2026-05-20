const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS detail_draft (
      id int(11) NOT NULL AUTO_INCREMENT,
      draft_id int(11) NOT NULL,
      nama_barang varchar(150) NOT NULL,
      tipe_barang varchar(50) NOT NULL DEFAULT 'inventaris' COMMENT 'inventaris | bhp',
      jumlah int(11) NOT NULL,
      harga_satuan decimal(15,2) NOT NULL,
      rasionalisasi text DEFAULT NULL,
      link_pembelian text DEFAULT NULL,
      foto_url varchar(255) DEFAULT NULL,
      status_item varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending | approved | rejected',
      PRIMARY KEY (id),
      KEY draft_id (draft_id),
      CONSTRAINT detail_draft_ibfk_1 FOREIGN KEY (draft_id) REFERENCES draft_pengadaan (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "detail_draft" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS detail_draft`);
  console.log('🗑️  Table "detail_draft" dropped.');
}

module.exports = { up, down };
