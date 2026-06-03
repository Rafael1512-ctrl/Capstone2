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

  // GET /api/notifications
  static async showNotifications(req, res) {
    try {
      const db = require('../config/db');
      const notifications = [];

      if (req.user.role === 'kepala_lab') {
        const [drafts] = await db.query(
          `SELECT d.*, u.nama AS kaprodi_nama
           FROM draft_pengadaan d
           LEFT JOIN users u ON d.ketua_prodi_id = u.id
           WHERE d.user_id = ? AND d.status IN ('finalized', 'rejected')
           ORDER BY d.id DESC LIMIT 10`,
          [req.user.id]
        );
        drafts.forEach(d => {
          if (d.status === 'finalized') {
            notifications.push({
              title: 'Draf Disetujui',
              message: `Draf pengadaan tahun ${d.tahun} telah disetujui/difinalisasi oleh Kaprodi ${d.kaprodi_nama || ''}.`,
              type: 'success',
              time: d.created_at
            });
          } else {
            notifications.push({
              title: 'Draf Ditolak',
              message: `Draf pengadaan tahun ${d.tahun} telah ditolak oleh Kaprodi ${d.kaprodi_nama || ''}.${d.alasan_penolakan ? ' Alasan: ' + d.alasan_penolakan : ''}`,
              type: 'danger',
              time: d.created_at
            });
          }
        });
      } else if (req.user.role === 'ketua_prodi') {
        const [drafts] = await db.query(
          `SELECT d.*, u.nama AS pengaju
           FROM draft_pengadaan d
           JOIN users u ON d.user_id = u.id
           WHERE d.status = 'submitted' AND d.ketua_prodi_id = ?
           ORDER BY d.id DESC LIMIT 10`,
          [req.user.id]
        );
        drafts.forEach(d => {
          notifications.push({
            title: 'Draf Menunggu Review',
            message: `Draf baru tahun ${d.tahun} telah diajukan oleh Kepala Lab ${d.pengaju} dan siap direview.`,
            type: 'info',
            time: d.created_at
          });
        });
      } else if (req.user.role === 'staf_admin') {
        const [drafts] = await db.query(
          `SELECT d.*, u.nama AS pengaju
           FROM draft_pengadaan d
           JOIN users u ON d.user_id = u.id
           WHERE d.status = 'finalized'
           ORDER BY d.id DESC LIMIT 10`
        );
        drafts.forEach(d => {
          notifications.push({
            title: 'Draf Pengadaan Baru',
            message: `Draf pengadaan tahun ${d.tahun} (diajukan oleh ${d.pengaju}) telah difinalisasi. Silakan proses penerimaan barang.`,
            type: 'success',
            time: d.created_at
          });
        });
      } else if (req.user.role === 'staf_lab') {
        const [bhps] = await db.query(
          `SELECT b.*, r.nama_ruangan FROM bhp b
           LEFT JOIN ruangan r ON b.ruangan_id = r.id
           WHERE b.stok <= b.stok_minimum LIMIT 10`
        );
        bhps.forEach(b => {
          notifications.push({
            title: 'Peringatan Stok BHP',
            message: `Stok ${b.nama_bhp} di ${b.nama_ruangan || 'Gudang'} tersisa ${b.stok} ${b.satuan} (Batas minimum: ${b.stok_minimum}).`,
            type: 'warning',
            time: b.created_at
          });
        });
      }

      res.json({
        success: true,
        notifications
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }
}

module.exports = DashboardController;
