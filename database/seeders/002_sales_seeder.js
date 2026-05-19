const db = require('../../config/db');

async function run() {
  // Ambil ID dari produk yang di-seed agar relationship foreign key valid
  const [rows] = await db.query('SELECT id, code FROM products');
  const productMap = {};
  rows.forEach(row => {
    productMap[row.code] = row.id;
  });

  const sales = [
    [productMap['PRD007'], 1, 2499.00, 'Completed'],
    [productMap['PRD009'], 1,  549.00, 'Processing'],
    [productMap['PRD008'], 1,  799.00, 'Completed'],
    [productMap['PRD003'], 1,  799.00, 'Pending'],
    [productMap['PRD006'], 1,  299.00, 'Cancelled'],
    [productMap['PRD001'], 5,  499.95, 'Completed'],
    [productMap['PRD002'], 10, 899.90, 'Completed'],
    [productMap['PRD005'], 3,   96.00, 'Completed']
  ];

  // Kosongkan tabel sales sebelum seeding agar bersih
  await db.query('SET FOREIGN_KEY_CHECKS = 0');
  await db.query('TRUNCATE TABLE sales');
  await db.query('SET FOREIGN_KEY_CHECKS = 1');

  for (const sale of sales) {
    if (sale[0]) { // Pastikan product_id ada
      await db.query(
        `INSERT INTO sales (product_id, qty, total, status) VALUES (?, ?, ?, ?)`,
        sale
      );
    }
  }
  console.log('🌱  Seeded "sales" table.');
}

module.exports = { run };
