const Product = require('../models/Product');
const Sales = require('../models/Sales');
const DetailDraft = require('../models/DetailDraft');

class DashboardController {
  // GET /
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

      res.render('index', {
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
        // Helper flags
        hasTopProducts: topProducts.length > 0,
        hasLowStockProducts: lowStockProducts.length > 0,
        hasRecentSales: recentSales.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // GET /inventory
  static async showInventory(req, res) {
    try {
      const products = await Product.getAll();
      res.render('inventory', {
        title: 'Inventory',
        activePath: '/inventory',
        products,
        // Helper flags
        hasProducts: products.length > 0,
        productCount: products.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // GET /create-product
  static async showCreateProduct(req, res) {
    res.render('create-product', {
      title: 'Add Product',
      activePath: '/create-product'
    });
  }

  // POST /create-product
  static async handleCreateProduct(req, res) {
    try {
      const { productName, productSKU, productPrice, productStock, productCategory, productDescription } = req.body;
      await Product.create(productSKU, productName, productCategory, productPrice, productStock, productDescription);
      res.redirect('/inventory');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // GET /reports
  static async showReports(req, res) {
    try {
      const totalRevenue = await Sales.getTotalRevenue();
      const productsSold = await Sales.getTotalProductsSold();
      const reportStats = await Product.getReportStats();
      const topProducts = await Sales.getTopProductsForReport();

      res.render('reports', {
        title: 'Reports',
        activePath: '/reports',
        totalRevenue,
        productsSold,
        lowStock: reportStats.lowStock,
        outOfStock: reportStats.outOfStock,
        topProducts,
        // Helper flags
        hasTopProducts: topProducts.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // GET /docs
  static async showDocs(req, res) {
    res.render('docs', {
      title: 'Documentation',
      activePath: '/docs'
    });
  }
}

module.exports = DashboardController;
