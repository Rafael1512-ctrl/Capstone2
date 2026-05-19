const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS products (
      id          INT AUTO_INCREMENT PRIMARY KEY,
      code        VARCHAR(20)    NOT NULL UNIQUE,
      name        VARCHAR(100)   NOT NULL,
      category    VARCHAR(50)    NOT NULL,
      brand       VARCHAR(50)    NOT NULL,
      price       DECIMAL(10,2)  NOT NULL,
      unit        VARCHAR(20)    NOT NULL DEFAULT 'pcs',
      quantity    INT            NOT NULL DEFAULT 0,
      image       VARCHAR(100)   NOT NULL DEFAULT 'product-1.png',
      description TEXT,
      created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
    )
  `);
  console.log('✅  Table "products" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS products`);
  console.log('🗑️  Table "products" dropped.');
}

module.exports = { up, down };
