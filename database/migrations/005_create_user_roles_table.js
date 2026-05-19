const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS user_roles (
      id int(11) NOT NULL AUTO_INCREMENT,
      user_id int(11) NOT NULL,
      role_id int(11) NOT NULL,
      PRIMARY KEY (id),
      UNIQUE KEY uq_user_role (user_id, role_id),
      KEY role_id (role_id),
      CONSTRAINT user_roles_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
      CONSTRAINT user_roles_ibfk_2 FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  `);
  console.log('✅  Table "user_roles" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS user_roles`);
  console.log('🗑️  Table "user_roles" dropped.');
}

module.exports = { up, down };
