const db = require('../config/db');

class Product {
  // Get all products
  static async getAll() {
    const [rows] = await db.query(`SELECT * FROM products ORDER BY code ASC`);
    return rows;
  }

  // Get low stock products
  static async getLowStock(limit = 5) {
    const [rows] = await db.query(
      `SELECT id, code, name, image, quantity
       FROM products
       WHERE quantity <= 10
       ORDER BY quantity ASC
       LIMIT ?`,
      [limit]
    );
    return rows;
  }

  // Get top products by sales
  static async getTopProducts(limit = 5) {
    const [rows] = await db.query(
      `SELECT p.name, p.image, p.price,
              COALESCE(SUM(s.qty),0) AS units_sold
       FROM products p
       LEFT JOIN sales s ON s.product_id = p.id AND s.status = 'Completed'
       GROUP BY p.id
       ORDER BY units_sold DESC
       LIMIT ?`,
      [limit]
    );
    return rows;
  }

  // Create product
  static async create(code, name, category, price, quantity, description = '') {
    const [result] = await db.query(
      `INSERT INTO products (code, name, category, brand, price, unit, quantity, description)
       VALUES (?, ?, ?, 'Unknown', ?, 'pcs', ?, ?)`,
      [code, name, category, price, quantity, description]
    );
    return result.insertId;
  }

  // Get stats for dashboard
  static async getStats() {
    const [[{ totalProducts }]] = await db.query(
      `SELECT COUNT(*) AS totalProducts FROM products`
    );
    const [[{ lowStock }]] = await db.query(
      `SELECT COUNT(*) AS lowStock FROM products WHERE quantity <= 10`
    );
    const [[{ outOfStock }]] = await db.query(
      `SELECT COUNT(*) AS outOfStock FROM products WHERE quantity = 0`
    );

    return { totalProducts, lowStock, outOfStock };
  }

  // Get report stats
  static async getReportStats() {
    const [[{ lowStock }]] = await db.query(
      `SELECT COUNT(*) AS lowStock FROM products WHERE quantity > 0 AND quantity <= 10`
    );
    const [[{ outOfStock }]] = await db.query(
      `SELECT COUNT(*) AS outOfStock FROM products WHERE quantity = 0`
    );

    return { lowStock, outOfStock };
  }
}

module.exports = Product;
