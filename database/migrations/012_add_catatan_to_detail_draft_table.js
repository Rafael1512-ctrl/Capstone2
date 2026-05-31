const db = require("../../config/db");

async function up() {
  await db.query(`
    ALTER TABLE detail_draft
    ADD COLUMN catatan text DEFAULT NULL AFTER status_item
  `);
  console.log('✅  Column "catatan" added to detail_draft table.');
}

async function down() {
  await db.query(`
    ALTER TABLE detail_draft
    DROP COLUMN IF EXISTS catatan
  `);
  console.log('🗑️  Column "catatan" dropped from detail_draft table.');
}

module.exports = { up, down };
