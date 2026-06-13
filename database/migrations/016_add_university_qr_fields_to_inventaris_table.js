const db = require('../../config/db');

async function up() {
  await db.query(`
    ALTER TABLE inventaris
      ADD COLUMN qr_univ_path varchar(255) DEFAULT NULL,
      ADD COLUMN kode_inventaris_univ varchar(100) DEFAULT NULL,
      ADD COLUMN tanggal_daftar_univ date DEFAULT NULL;
  `);
  console.log('✅  Added university QR fields to inventaris table.');
}

async function down() {
  await db.query(`
    ALTER TABLE inventaris
      DROP COLUMN qr_univ_path,
      DROP COLUMN kode_inventaris_univ,
      DROP COLUMN tanggal_daftar_univ;
  `);
  console.log('🗑️  Removed university QR fields from inventaris table.');
}

module.exports = { up, down };