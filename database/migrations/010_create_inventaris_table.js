const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS inventaris (
      id int(11) NOT NULL AUTO_INCREMENT,
      ruangan_id int(11) NOT NULL,
      detail_draft_id int(11) DEFAULT NULL,
      nama_barang varchar(150) NOT NULL,
      nomor_label varchar(50) DEFAULT NULL,
      barcode_qr varchar(100) DEFAULT NULL,
      kondisi varchar(20) NOT NULL DEFAULT 'baik' COMMENT 'baik | rusak_ringan | rusak_berat | dihapus',
      tanggal_terima date DEFAULT NULL,
      updated_at timestamp NULL DEFAULT current_timestamp(),
      PRIMARY KEY (id),
      UNIQUE KEY nomor_label (nomor_label),
      UNIQUE KEY barcode_qr (barcode_qr),
      KEY ruangan_id (ruangan_id),
      KEY detail_draft_id (detail_draft_id),
      CONSTRAINT inventaris_ibfk_1 FOREIGN KEY (ruangan_id) REFERENCES ruangan (id) ON DELETE CASCADE,
      CONSTRAINT inventaris_ibfk_2 FOREIGN KEY (detail_draft_id) REFERENCES detail_draft (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "inventaris" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS inventaris`);
  console.log('🗑️  Table "inventaris" dropped.');
}

module.exports = { up, down };
