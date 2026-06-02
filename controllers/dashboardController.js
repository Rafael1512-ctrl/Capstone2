const Product = require('../models/Product');
const Sales = require('../models/Sales');
const DetailDraft = require('../models/DetailDraft');

class DashboardController {
  // GET /api/
  static async showDashboard(req, res) {
    try {
      const db = require('../config/db');

      // 1. Get Inventory Condition Statistics
      const [kondisiRows] = await db.query(
        `SELECT kondisi, COUNT(*) as count 
         FROM inventaris 
         WHERE kondisi != 'dihapus' 
         GROUP BY kondisi`
      );
      const conditions = { baik: 0, rusak_ringan: 0, rusak_berat: 0 };
      kondisiRows.forEach(row => {
        if (conditions.hasOwnProperty(row.kondisi)) {
          conditions[row.kondisi] = row.count;
        }
      });

      // 2. Get Annual Procurement Expenses
      const [expenseRows] = await db.query(
        `SELECT dp.tahun, COALESCE(SUM(dd.jumlah * dd.harga_satuan), 0) as total_expense
         FROM detail_draft dd
         JOIN draft_pengadaan dp ON dd.draft_id = dp.id
         WHERE dd.status_item = 'approved'
         GROUP BY dp.tahun
         ORDER BY dp.tahun ASC`
      );
      const expenses = {
        years: expenseRows.map(r => r.tahun.toString()),
        values: expenseRows.map(r => parseFloat(r.total_expense))
      };

      // 3. Get Low Stock BHP Alert Count & Items
      const [lowStockBhpRows] = await db.query(
        `SELECT b.*, r.nama_ruangan
         FROM bhp b
         LEFT JOIN ruangan r ON b.ruangan_id = r.id
         WHERE b.stok <= b.stok_minimum`
      );

      // 4. Out of stock BHP count
      const [[{ count: outOfStockBhp }]] = await db.query(
        `SELECT COUNT(*) as count FROM bhp WHERE stok = 0`
      );

      // 5. Total counts
      const [[{ count: totalAset }]] = await db.query("SELECT COUNT(*) as count FROM inventaris WHERE kondisi != 'dihapus'");
      const [[{ count: totalBhp }]] = await db.query("SELECT COUNT(*) as count FROM bhp");
      const [[{ count: totalMaintenance }]] = await db.query("SELECT COUNT(*) as count FROM maintenance_log");

      // 6. Recent assets received
      const [recentAssets] = await db.query(
        `SELECT iv.id, iv.nomor_label, iv.kondisi, iv.tanggal_terima, dd.nama_barang, dd.harga_satuan
         FROM inventaris iv
         JOIN detail_draft dd ON iv.detail_draft_id = dd.id
         WHERE iv.kondisi != 'dihapus'
         ORDER BY iv.id DESC
         LIMIT 5`
      );

      // 7. Recent maintenance logs
      const [recentMaintenance] = await db.query(
        `SELECT m.id, m.tanggal_maintenance, m.kondisi_sebelum, m.kondisi_sesudah, m.deskripsi,
                i.nama_barang AS nama_aset, i.nomor_label, u.nama AS petugas
         FROM maintenance_log m
         JOIN inventaris i ON m.inventaris_id = i.id
         JOIN users u ON m.staf_lab_id = u.id
         ORDER BY m.id DESC
         LIMIT 5`
      );

      const DetailDraft = require('../models/DetailDraft');
      const totalExpenses = await DetailDraft.getTotalExpenses();

      res.json({
        title: 'Dashboard',
        activePath: '/',
        totalSales: totalExpenses, // Re-mapped for Card 1
        totalProducts: totalAset,   // Re-mapped for Card 2
        lowStock: lowStockBhpRows.length, // Re-mapped for Card 3
        outOfStock: outOfStockBhp,  // Re-mapped for Card 4
        totalRefunds: 0,
        totalExpenses,
        totalProfit: 0,
        topProducts: [],
        lowStockProducts: [],
        recentSales: [],
        hasTopProducts: false,
        hasLowStockProducts: false,
        hasRecentSales: false,
        // Real dashboard statistics
        totalAset,
        totalBhp,
        totalMaintenance,
        recentAssets,
        recentMaintenance,
        chartData: {
          conditions,
          expenses
        },
        lowStockBhp: {
          count: lowStockBhpRows.length,
          items: lowStockBhpRows
        }
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
