const db = require('../../config/db');

async function up() {
  await db.query(`
    CREATE TABLE IF NOT EXISTS sales (
      id          INT AUTO_INCREMENT PRIMARY KEY,
      product_id  INT            NOT NULL,
      qty         INT            NOT NULL DEFAULT 1,
      total       DECIMAL(10,2)  NOT NULL,
      status      ENUM('Completed','Processing','Pending','Cancelled') NOT NULL DEFAULT 'Completed',
      sale_date   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )
  `);
  console.log('✅  Table "sales" created.');
}

async function down() {
  await db.query(`DROP TABLE IF EXISTS sales`);
  console.log('🗑️  Table "sales" dropped.');
}

module.exports = { up, down };
