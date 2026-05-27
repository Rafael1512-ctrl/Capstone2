const Product = require('../models/Product');
const Sales = require('../models/Sales');
const DetailDraft = require('../models/DetailDraft');

class DashboardController {
  // GET /api/
  static async showDashboard(req, res) {
    try {
      const stats = await Product.getStats();
      const topProducts = await Product.getTopProducts();
      const lowStockProducts = await Product.getLowStock();
      const recentSales = await Sales.getRecentSales();
      const totalSales = await Sales.getTotalSalesCompleted();
      const totalRefunds = await Sales.getTotalRefunds();
      const totalExpenses = await DetailDraft.getTotalExpenses();
      const totalProfit = Math.max(0, totalSales - totalExpenses);

      res.json({
        title: 'Dashboard',
        activePath: '/',
        totalSales,
        totalProducts: stats.totalProducts,
        lowStock: stats.lowStock,
        outOfStock: stats.outOfStock,
        totalRefunds,
        totalExpenses,
        totalProfit,
        topProducts,
        lowStockProducts,
        recentSales,
        hasTopProducts: topProducts.length > 0,
        hasLowStockProducts: lowStockProducts.length > 0,
        hasRecentSales: recentSales.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/inventory
  static async showInventory(req, res) {
    try {
      const products = await Product.getAll();
      res.json({
        title: 'Inventory',
        activePath: '/inventory',
        products,
        hasProducts: products.length > 0,
        productCount: products.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/create-product
  static async showCreateProduct(req, res) {
    res.json({
      title: 'Add Product',
      activePath: '/create-product'
    });
  }

  // POST /api/create-product
  static async handleCreateProduct(req, res) {
    try {
      const { productName, productSKU, productPrice, productStock, productCategory, productDescription } = req.body;
      await Product.create(productSKU, productName, productCategory, productPrice, productStock, productDescription);
      res.json({ success: true, message: 'Product created successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/reports
  static async showReports(req, res) {
    try {
      const totalRevenue = await Sales.getTotalRevenue();
      const productsSold = await Sales.getTotalProductsSold();
      const reportStats = await Product.getReportStats();
      const topProducts = await Sales.getTopProductsForReport();

      res.json({
        title: 'Reports',
        activePath: '/reports',
        totalRevenue,
        productsSold,
        lowStock: reportStats.lowStock,
        outOfStock: reportStats.outOfStock,
        topProducts,
        hasTopProducts: topProducts.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/docs
  static async showDocs(req, res) {
    res.json({
      title: 'Documentation',
      activePath: '/docs'
    });
  }
}

module.exports = DashboardController;
