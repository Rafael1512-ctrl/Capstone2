const db = require('../config/db');

async function run() {
  // Clean up old soft-deleted users
  console.log('=== Cleaning up soft-deleted users ===');
  const [softDeleted] = await db.query(
    `SELECT id, nama, email FROM users WHERE deleted_at IS NOT NULL`
  );
  console.log('Found soft-deleted users:', softDeleted);

  for (const u of softDeleted) {
    await db.query(`DELETE FROM user_roles WHERE user_id = ?`, [u.id]);
    await db.query(`DELETE FROM users WHERE id = ?`, [u.id]);
    console.log(`Deleted user ID ${u.id} (${u.email})`);
  }

  // Also clean up the test user we created
  const [testUser] = await db.query(
    `SELECT id FROM users WHERE email = 'testdebug@mail.com'`
  );
  for (const u of testUser) {
    await db.query(`DELETE FROM user_roles WHERE user_id = ?`, [u.id]);
    await db.query(`DELETE FROM users WHERE id = ?`, [u.id]);
    console.log(`Deleted test user ID ${u.id}`);
  }

  console.log('\n=== Remaining users ===');
  const [rows] = await db.query(
    `SELECT u.id, u.nama, u.email, r.nama AS role
     FROM users u
     LEFT JOIN user_roles ur ON ur.user_id = u.id
     LEFT JOIN roles r ON ur.role_id = r.id
     ORDER BY u.id DESC`
  );
  console.table(rows);

  process.exit(0);
}
run();
