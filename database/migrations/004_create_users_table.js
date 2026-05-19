const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS users (
      id int(11) NOT NULL AUTO_INCREMENT,
      nama varchar(100) NOT NULL,
      email varchar(100) NOT NULL,
      password varchar(255) NOT NULL,
      created_at timestamp NULL DEFAULT current_timestamp(),
      PRIMARY KEY (id),
      UNIQUE KEY email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "users" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS users`);
  console.log('🗑️  Table "users" dropped.');
}

module.exports = { up, down };
