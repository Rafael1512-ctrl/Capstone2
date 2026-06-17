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
  if (!columnNames.includes('phone')) {
    queries.push('ADD COLUMN phone varchar(15) DEFAULT NULL AFTER email');
  }
  if (!columnNames.includes('position')) {
    queries.push('ADD COLUMN position varchar(100) DEFAULT NULL AFTER phone');
  }
  if (!columnNames.includes('avatar')) {
    queries.push('ADD COLUMN avatar varchar(255) DEFAULT NULL AFTER position');
  }
  if (!columnNames.includes('updated_at')) {
    queries.push('ADD COLUMN updated_at timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER created_at');
  }

  if (queries.length > 0) {
    await db.query(`ALTER TABLE users ${queries.join(', ')}`);
    console.log('✅  Profile fields (phone, position, avatar, updated_at) added to users.');
  } else {
    console.log('ℹ️  Profile columns already exist in users.');
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
  if (columnNames.includes('phone')) drops.push('DROP COLUMN phone');
  if (columnNames.includes('position')) drops.push('DROP COLUMN position');
  if (columnNames.includes('avatar')) drops.push('DROP COLUMN avatar');
  if (columnNames.includes('updated_at')) drops.push('DROP COLUMN updated_at');

  if (drops.length > 0) {
    await db.query(`ALTER TABLE users ${drops.join(', ')}`);
    console.log('🗑️  Profile fields (phone, position, avatar, updated_at) removed from users.');
  }
}

module.exports = { up, down };
