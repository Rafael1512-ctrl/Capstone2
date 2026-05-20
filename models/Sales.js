const db = require('../config/db');

class Sales {
  // Get total sales completed
  static async getTotalSalesCompleted() {
    const [[{ totalSales }]] = await db.query(
      `SELECT COALESCE(SUM(total),0) AS totalSales FROM sales WHERE status='Completed'`
    );
    return totalSales;
  }

  // Get total refunds (cancelled)
  static async getTotalRefunds() {
    const [[{ totalRefunds }]] = await db.query(
      `SELECT COALESCE(SUM(total),0) AS totalRefunds FROM sales WHERE status='Cancelled'`
    );
    return totalRefunds;
  }

  // Get recent sales
  static async getRecentSales(limit = 5) {
    const [rows] = await db.query(
      `SELECT p.name, p.image, p.category, s.total, s.status, s.sale_date
       FROM sales s
       JOIN products p ON s.product_id = p.id
       ORDER BY s.sale_date DESC
       LIMIT ?`,
      [limit]
    );
    return rows;
  }

  // Get total revenue for reports
  static async getTotalRevenue() {
    const [[{ totalRevenue }]] = await db.query(
      `SELECT COALESCE(SUM(total),0) AS totalRevenue FROM sales WHERE status='Completed'`
    );
    return totalRevenue;
  }

  // Get total products sold for reports
  static async getTotalProductsSold() {
    const [[{ productsSold }]] = await db.query(
      `SELECT COALESCE(SUM(qty),0) AS productsSold FROM sales WHERE status='Completed'`
    );
    return productsSold;
  }

  // Get top products for reports
  static async getTopProductsForReport(limit = 3) {
    const [rows] = await db.query(
      `SELECT p.name, p.image,
              COALESCE(SUM(s.qty),0)    AS units_sold,
              COALESCE(SUM(s.total),0)  AS revenue
       FROM products p
       LEFT JOIN sales s ON s.product_id = p.id AND s.status='Completed'
       GROUP BY p.id
       ORDER BY revenue DESC
       LIMIT ?`,
      [limit]
    );
    return rows;
  }
}

module.exports = Sales;
