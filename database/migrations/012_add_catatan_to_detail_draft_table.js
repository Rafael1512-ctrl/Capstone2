const db = require("../../config/db");

async function up() {
  try {
    await db.query(`
      ALTER TABLE detail_draft
      ADD COLUMN catatan text DEFAULT NULL AFTER status_item
    `);
    console.log('✅  Column "catatan" added to detail_draft table.');
  } catch (err) {
    // Abaikan jika kolom 'catatan' sudah ada
    if (err.code !== 'ER_DUP_FIELDNAME') {
      throw err;
    }
    console.log('ℹ️  Column "catatan" already exists in detail_draft table.');
  }
}

async function down() {
  try {
    await db.query(`
      ALTER TABLE detail_draft
      DROP COLUMN catatan
    `);
    console.log('🗑️  Column "catatan" dropped from detail_draft table.');
  } catch (err) {
    // Abaikan jika kolom tidak ditemukan saat rollback
    if (err.code !== 'ER_CANT_DROP_FIELD_OR_KEY') {
      throw err;
    }
  }
}

module.exports = { up, down };
