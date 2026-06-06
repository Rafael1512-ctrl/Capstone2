const db = require('../../config/db');

async function up() {
  try {
    await db.query(`
      ALTER TABLE detail_draft
      ADD COLUMN kategori varchar(100) DEFAULT NULL AFTER nama_barang,
      ADD COLUMN jenis varchar(100) DEFAULT NULL AFTER kategori
    `);
    console.log('✅ Columns "kategori" and "jenis" added to detail_draft table.');
  } catch (err) {
    if (err.code !== 'ER_DUP_FIELDNAME') {
      throw err;
    }
    console.log('ℹ️ Columns "kategori" and "jenis" already exist in detail_draft table.');
  }

  try {
    await db.query(`
      ALTER TABLE inventaris
      ADD COLUMN kategori varchar(100) DEFAULT NULL AFTER nama_barang,
      ADD COLUMN jenis varchar(100) DEFAULT NULL AFTER kategori
    `);
    console.log('✅ Columns "kategori" and "jenis" added to inventaris table.');
  } catch (err) {
    if (err.code !== 'ER_DUP_FIELDNAME') {
      throw err;
    }
    console.log('ℹ️ Columns "kategori" and "jenis" already exist in inventaris table.');
  }
}

async function down() {
  try {
    await db.query(`
      ALTER TABLE detail_draft
      DROP COLUMN kategori,
      DROP COLUMN jenis
    `);
    console.log('🗑️ Columns "kategori" and "jenis" dropped from detail_draft table.');
  } catch (err) {
    // Ignore if columns do not exist
  }

  try {
    await db.query(`
      ALTER TABLE inventaris
      DROP COLUMN kategori,
      DROP COLUMN jenis
    `);
    console.log('🗑️ Columns "kategori" and "jenis" dropped from inventaris table.');
  } catch (err) {
    // Ignore if columns do not exist
  }
}

module.exports = { up, down };
