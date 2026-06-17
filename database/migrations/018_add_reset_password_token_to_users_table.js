const db = require('../../config/db');

async function up() {
  const [columns] = await db.query(`
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
  `);
  const columnNames = columns.map((col) => col.COLUMN_NAME);

  const queries = [];
  if (!columnNames.includes('reset_token')) {
    queries.push('ADD COLUMN reset_token varchar(255) DEFAULT NULL AFTER avatar');
  }
  if (!columnNames.includes('reset_token_expires')) {
    queries.push('ADD COLUMN reset_token_expires datetime DEFAULT NULL AFTER reset_token');
  }

  if (queries.length > 0) {
    await db.query(`ALTER TABLE users ${queries.join(', ')}`);
    console.log('✅  Reset password token fields added to users table.');
  } else {
    console.log('ℹ️  Reset password token columns already exist in users.');
  }
}

async function down() {
  const [columns] = await db.query(`
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
  `);
  const columnNames = columns.map((col) => col.COLUMN_NAME);

  const drops = [];
  if (columnNames.includes('reset_token')) drops.push('DROP COLUMN reset_token');
  if (columnNames.includes('reset_token_expires')) drops.push('DROP COLUMN reset_token_expires');

  if (drops.length > 0) {
    await db.query(`ALTER TABLE users ${drops.join(', ')}`);
    console.log('🗑️  Reset password token fields removed from users table.');
  }
}

module.exports = { up, down };
