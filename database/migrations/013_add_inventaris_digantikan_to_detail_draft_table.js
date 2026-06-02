const db = require('../../config/db');

async function up() {
  try {
    await db.query(`
      ALTER TABLE detail_draft
      ADD COLUMN inventaris_digantikan_id int(11) DEFAULT NULL AFTER link_pembelian,
      ADD CONSTRAINT fk_detail_draft_inventaris_digantikan FOREIGN KEY (inventaris_digantikan_id) REFERENCES inventaris (id) ON DELETE SET NULL
    `);
    console.log('✅  Column "inventaris_digantikan_id" and foreign key added to detail_draft table.');
  } catch (err) {
    if (err.code !== 'ER_DUP_FIELDNAME') {
      throw err;
    }
    console.log('ℹ️  Column "inventaris_digantikan_id" already exists in detail_draft table.');
  }
}

async function down() {
  try {
    // Check if foreign key exists and drop it, then drop column
    await db.query(`
      ALTER TABLE detail_draft
      DROP FOREIGN KEY fk_detail_draft_inventaris_digantikan
    `);
  } catch (err) {
    // Ignore key not existing
  }

  try {
    await db.query(`
      ALTER TABLE detail_draft
      DROP COLUMN inventaris_digantikan_id
    `);
    console.log('🗑️  Column "inventaris_digantikan_id" dropped from detail_draft table.');
  } catch (err) {
    // Ignore column not existing
  }
}

module.exports = { up, down };
