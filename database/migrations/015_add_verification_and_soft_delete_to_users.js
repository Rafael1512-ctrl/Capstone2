const db = require('../../config/db');

async function up() {
  const [columns] = await db.query(`
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
  `);
  const columnNames = columns.map((col) => col.COLUMN_NAME);

  if (!columnNames.includes('email_verified_at')) {
    await db.query(`
      ALTER TABLE users
      ADD COLUMN email_verified_at timestamp NULL DEFAULT NULL AFTER password
    `);
    console.log('✅  Column "email_verified_at" added to users.');
  }

  if (!columnNames.includes('deleted_at')) {
    await db.query(`
      ALTER TABLE users
      ADD COLUMN deleted_at timestamp NULL DEFAULT NULL AFTER created_at
    `);
    console.log('✅  Column "deleted_at" added to users.');
  }

  await db.query(`
    UPDATE users
    SET email_verified_at = COALESCE(email_verified_at, created_at, NOW())
    WHERE email_verified_at IS NULL
  `);
  console.log('✅  Existing users marked as verified.');
}

async function down() {
  const [columns] = await db.query(`
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
  `);
  const columnNames = columns.map((col) => col.COLUMN_NAME);

  if (columnNames.includes('deleted_at')) {
    await db.query(`ALTER TABLE users DROP COLUMN deleted_at`);
    console.log('🗑️  Column "deleted_at" dropped from users.');
  }

  if (columnNames.includes('email_verified_at')) {
    await db.query(`ALTER TABLE users DROP COLUMN email_verified_at`);
    console.log('🗑️  Column "email_verified_at" dropped from users.');
  }
}

module.exports = { up, down };
