const db = require('../../config/db');

async function up() {
  const [columns] = await db.query(`
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inventaris'
  `);
  const columnNames = columns.map((col) => col.COLUMN_NAME);

  const queries = [];
  if (!columnNames.includes('qr_univ_path')) {
    queries.push('ADD COLUMN qr_univ_path varchar(255) DEFAULT NULL');
  }
  if (!columnNames.includes('kode_inventaris_univ')) {
    queries.push('ADD COLUMN kode_inventaris_univ varchar(100) DEFAULT NULL');
  }
  if (!columnNames.includes('tanggal_daftar_univ')) {
    queries.push('ADD COLUMN tanggal_daftar_univ date DEFAULT NULL');
  }

  if (queries.length > 0) {
    await db.query(`ALTER TABLE inventaris ${queries.join(', ')}`);
    console.log('✅  Added university QR fields to inventaris table.');
  } else {
    console.log('ℹ️  University QR fields already exist in inventaris table.');
  }
}

async function down() {
  const [columns] = await db.query(`
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inventaris'
  `);
  const columnNames = columns.map((col) => col.COLUMN_NAME);

  const drops = [];
  if (columnNames.includes('qr_univ_path')) drops.push('DROP COLUMN qr_univ_path');
  if (columnNames.includes('kode_inventaris_univ')) drops.push('DROP COLUMN kode_inventaris_univ');
  if (columnNames.includes('tanggal_daftar_univ')) drops.push('DROP COLUMN tanggal_daftar_univ');

  if (drops.length > 0) {
    await db.query(`ALTER TABLE inventaris ${drops.join(', ')}`);
    console.log('🗑️  Removed university QR fields from inventaris table.');
  }
}

module.exports = { up, down };