const express = require('express');
const router = express.Router();
const db = require('../config/db');
const bcrypt = require('bcryptjs');

// ─── MIDDLEWARES ──────────────────────────────────────────────────────────────
function requireAuth(req, res, next) {
  if (!req.session.user) {
    return res.redirect('/signin');
  }
  next();
}

function requireRole(role) {
  return (req, res, next) => {
    if (!req.session.user || req.session.user.role !== role) {
      return res.status(403).render('404-error', {
        title: 'Forbidden',
        message: 'Anda tidak memiliki hak akses untuk halaman ini.'
      });
    }
    next();
  };
}

// ─── AUTHENTICATION ROUTES ────────────────────────────────────────────────────
router.get('/signin', (req, res) => {
  if (req.session.user) return res.redirect('/');
  res.render('signin', { title: 'Sign In', error: req.query.error });
});

router.post('/signin', async (req, res) => {
  try {
    const { email, password } = req.body;
    if (!email || !password) {
      return res.render('signin', { title: 'Sign In', error: 'Email dan password harus diisi.' });
    }

    const [rows] = await db.query(
      `SELECT u.*, r.nama AS role 
       FROM users u
       JOIN user_roles ur ON ur.user_id = u.id
       JOIN roles r ON ur.role_id = r.id
       WHERE u.email = ?`,
      [email]
    );

    if (rows.length === 0) {
      return res.render('signin', { title: 'Sign In', error: 'Akun tidak ditemukan.' });
    }

    const user = rows[0];
    const isMatch = bcrypt.compareSync(password, user.password);
    if (!isMatch) {
      return res.render('signin', { title: 'Sign In', error: 'Password salah.' });
    }

    req.session.user = {
      id: user.id,
      nama: user.nama,
      email: user.email,
      role: user.role
    };

    res.redirect('/');
  } catch (err) {
    console.error(err);
    res.render('signin', { title: 'Sign In', error: 'Server error: ' + err.message });
  }
});

router.get('/signout', (req, res) => {
  req.session.destroy();
  res.redirect('/signin');
});

// ─── DASHBOARD ─────────────────────────────────────────────────────────────────
router.get('/', requireAuth, async (req, res) => {
  try {
    const [[{ totalSales }]] = await db.query(`SELECT COALESCE(SUM(total),0) AS totalSales FROM sales WHERE status='Completed'`);
    const [[{ totalProducts }]] = await db.query(`SELECT COUNT(*) AS totalProducts FROM products`);
    const [[{ lowStock }]] = await db.query(`SELECT COUNT(*) AS lowStock FROM products WHERE quantity <= 10`);
    const [[{ outOfStock }]] = await db.query(`SELECT COUNT(*) AS outOfStock FROM products WHERE quantity = 0`);

    const [[{ totalRefunds }]] = await db.query(`SELECT COALESCE(SUM(total),0) AS totalRefunds FROM sales WHERE status='Cancelled'`);
    const [[{ totalExpenses }]] = await db.query(`SELECT COALESCE(SUM(jumlah * harga_satuan),0) AS totalExpenses FROM detail_draft WHERE status_item='approved'`);
    const totalProfit = Math.max(0, totalSales - totalExpenses);

    const [topProducts] = await db.query(`
      SELECT p.name, p.image, p.price,
             COALESCE(SUM(s.qty),0) AS units_sold
      FROM products p
      LEFT JOIN sales s ON s.product_id = p.id AND s.status = 'Completed'
      GROUP BY p.id
      ORDER BY units_sold DESC
      LIMIT 5
    `);

    const [lowStockProducts] = await db.query(`
      SELECT id, code, name, image, quantity
      FROM products
      WHERE quantity <= 10
      ORDER BY quantity ASC
      LIMIT 5
    `);

    const [recentSales] = await db.query(`
      SELECT p.name, p.image, p.category, s.total, s.status, s.sale_date
      FROM sales s
      JOIN products p ON s.product_id = p.id
      ORDER BY s.sale_date DESC
      LIMIT 5
    `);

    res.render('index', {
      title: 'Dashboard',
      activePath: '/',
      totalSales,
      totalProducts,
      lowStock,
      outOfStock,
      totalRefunds,
      totalExpenses,
      totalProfit,
      topProducts,
      lowStockProducts,
      recentSales
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

// ─── GENERAL INVENTORY ─────────────────────────────────────────────────────────
router.get('/inventory', requireAuth, async (req, res) => {
  try {
    const [products] = await db.query(`SELECT * FROM products ORDER BY code ASC`);
    res.render('inventory', {
      title: 'Inventory',
      activePath: '/inventory',
      products
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.get('/create-product', requireAuth, (req, res) => {
  res.render('create-product', {
    title: 'Add Product',
    activePath: '/create-product'
  });
});

router.post('/create-product', requireAuth, async (req, res) => {
  try {
    const { productName, productSKU, productPrice, productStock, productCategory, productDescription } = req.body;
    await db.query(
      `INSERT INTO products (code, name, category, brand, price, unit, quantity, description)
       VALUES (?, ?, ?, 'Unknown', ?, 'pcs', ?, ?)`,
      [productSKU, productName, productCategory, productPrice, productStock, productDescription || '']
    );
    res.redirect('/inventory');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

// ─── GENERAL REPORTS ───────────────────────────────────────────────────────────
router.get('/reports', requireAuth, async (req, res) => {
  try {
    const [[{ totalRevenue }]] = await db.query(`SELECT COALESCE(SUM(total),0) AS totalRevenue FROM sales WHERE status='Completed'`);
    const [[{ productsSold }]] = await db.query(`SELECT COALESCE(SUM(qty),0) AS productsSold FROM sales WHERE status='Completed'`);
    const [[{ lowStock }]] = await db.query(`SELECT COUNT(*) AS lowStock FROM products WHERE quantity > 0 AND quantity <= 10`);
    const [[{ outOfStock }]] = await db.query(`SELECT COUNT(*) AS outOfStock FROM products WHERE quantity = 0`);

    const [topProducts] = await db.query(`
      SELECT p.name, p.image,
             COALESCE(SUM(s.qty),0)    AS units_sold,
             COALESCE(SUM(s.total),0)  AS revenue
      FROM products p
      LEFT JOIN sales s ON s.product_id = p.id AND s.status='Completed'
      GROUP BY p.id
      ORDER BY revenue DESC
      LIMIT 3
    `);

    res.render('reports', {
      title: 'Reports',
      activePath: '/reports',
      totalRevenue,
      productsSold,
      lowStock,
      outOfStock,
      topProducts
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

// ─── GENERAL DOCS ──────────────────────────────────────────────────────────────
router.get('/docs', requireAuth, (req, res) => {
  res.render('docs', {
    title: 'Documentation',
    activePath: '/docs'
  });
});

// ─── ROLE: ADMINISTRATOR ───────────────────────────────────────────────────────
router.get('/admin/users', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    const [users] = await db.query(`
      SELECT u.id, u.nama, u.email, r.nama AS role, r.id AS role_id
      FROM users u
      LEFT JOIN user_roles ur ON ur.user_id = u.id
      LEFT JOIN roles r ON ur.role_id = r.id
      ORDER BY u.id DESC
    `);
    const [roles] = await db.query(`SELECT * FROM roles`);
    res.render('admin/users', {
      title: 'Kelola Pengguna',
      activePath: '/admin/users',
      users,
      roles
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/admin/users/create', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    const { nama, email, password, role_id } = req.body;
    const hashedPassword = bcrypt.hashSync(password || 'password', 10);
    const [result] = await db.query(
      `INSERT INTO users (nama, email, password) VALUES (?, ?, ?)`,
      [nama, email, hashedPassword]
    );
    await db.query(
      `INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)`,
      [result.insertId, role_id]
    );
    res.redirect('/admin/users');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/admin/users/delete/:id', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    await db.query(`DELETE FROM user_roles WHERE user_id = ?`, [req.params.id]);
    await db.query(`DELETE FROM users WHERE id = ?`, [req.params.id]);
    res.redirect('/admin/users');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/admin/users/edit/:id', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    const { nama, email, password, role_id } = req.body;
    if (password && password.trim() !== '') {
      const hashedPassword = bcrypt.hashSync(password, 10);
      await db.query(
        `UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?`,
        [nama, email, hashedPassword, req.params.id]
      );
    } else {
      await db.query(
        `UPDATE users SET nama = ?, email = ? WHERE id = ?`,
        [nama, email, req.params.id]
      );
    }
    await db.query(`DELETE FROM user_roles WHERE user_id = ?`, [req.params.id]);
    await db.query(
      `INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)`,
      [req.params.id, role_id]
    );
    res.redirect('/admin/users');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.get('/admin/ruangan', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    const [ruangan] = await db.query(`SELECT * FROM ruangan ORDER BY id DESC`);
    res.render('admin/ruangan', {
      title: 'Kelola Ruangan',
      activePath: '/admin/ruangan',
      ruangan
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/admin/ruangan/create', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    const { nama_ruangan, kode_ruangan, lokasi } = req.body;
    await db.query(
      `INSERT INTO ruangan (nama_ruangan, kode_ruangan, lokasi) VALUES (?, ?, ?)`,
      [nama_ruangan, kode_ruangan, lokasi]
    );
    res.redirect('/admin/ruangan');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/admin/ruangan/delete/:id', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    await db.query(`DELETE FROM ruangan WHERE id = ?`, [req.params.id]);
    res.redirect('/admin/ruangan');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/admin/ruangan/edit/:id', requireAuth, requireRole('admin'), async (req, res) => {
  try {
    const { nama_ruangan, kode_ruangan, lokasi } = req.body;
    await db.query(
      `UPDATE ruangan SET nama_ruangan = ?, kode_ruangan = ?, lokasi = ? WHERE id = ?`,
      [nama_ruangan, kode_ruangan, lokasi, req.params.id]
    );
    res.redirect('/admin/ruangan');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

// ─── ROLE: KEPALA LABORATORIUM ─────────────────────────────────────────────────
router.get('/kepala-lab/pengadaan', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    // Cari draf aktif
    const [drafts] = await db.query(
      `SELECT * FROM draft_pengadaan WHERE user_id = ? AND status = 'draft'`,
      [req.session.user.id]
    );

    let activeDraft = null;
    let items = [];

    if (drafts.length > 0) {
      activeDraft = drafts[0];
      [items] = await db.query(
        `SELECT * FROM detail_draft WHERE draft_id = ?`,
        [activeDraft.id]
      );
    }

    const [kaprodiList] = await db.query(`SELECT u.id, u.nama FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id WHERE r.nama = 'ketua_prodi'`);

    res.render('kepala_lab/pengadaan', {
      title: 'Draf Pengadaan Baru',
      activePath: '/kepala-lab/pengadaan',
      activeDraft,
      items,
      kaprodiList
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/kepala-lab/pengadaan/create-draft', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    const { tahun, ketua_prodi_id } = req.body;
    await db.query(
      `INSERT INTO draft_pengadaan (user_id, ketua_prodi_id, tahun, status) VALUES (?, ?, ?, 'draft')`,
      [req.session.user.id, ketua_prodi_id || null, tahun || new Date().getFullYear()]
    );
    res.redirect('/kepala-lab/history');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/kepala-lab/pengadaan/update-draft/:id', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    const { tahun, ketua_prodi_id } = req.body;
    await db.query(
      `UPDATE draft_pengadaan SET tahun = ?, ketua_prodi_id = ? WHERE id = ? AND user_id = ? AND status = 'draft'`,
      [tahun, ketua_prodi_id || null, req.params.id, req.session.user.id]
    );
    res.redirect('/kepala-lab/history');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/kepala-lab/pengadaan/delete-draft/:id', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    await db.query(`DELETE FROM draft_pengadaan WHERE id = ? AND user_id = ? AND status = 'draft'`, [req.params.id, req.session.user.id]);
    res.redirect('/kepala-lab/history');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/kepala-lab/pengadaan/add-item', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    const { draft_id, nama_barang, tipe_barang, harga_satuan, jumlah, rasionalisasi, link_pembelian } = req.body;
    await db.query(
      `INSERT INTO detail_draft (draft_id, nama_barang, tipe_barang, harga_satuan, jumlah, rasionalisasi, link_pembelian, status_item)
       VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')`,
      [draft_id, nama_barang, tipe_barang, harga_satuan, jumlah, rasionalisasi || '', link_pembelian || '']
    );
    res.redirect('/kepala-lab/history');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/kepala-lab/pengadaan/delete-item/:id', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    await db.query(`DELETE FROM detail_draft WHERE id = ?`, [req.params.id]);
    res.redirect('/kepala-lab/history');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/kepala-lab/pengadaan/submit/:id', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    await db.query(`UPDATE draft_pengadaan SET status = 'submitted' WHERE id = ?`, [req.params.id]);
    res.redirect('/kepala-lab/history');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.get('/kepala-lab/history', requireAuth, requireRole('kepala_lab'), async (req, res) => {
  try {
    const [drafts] = await db.query(
      `SELECT d.*, (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id) AS item_count
       FROM draft_pengadaan d
       WHERE d.user_id = ? AND d.status != 'draft'
       ORDER BY d.id DESC`,
      [req.session.user.id]
    );

    // Load active draft (status=draft) so user can add items here
    const [activeDrafts] = await db.query(
      `SELECT * FROM draft_pengadaan WHERE user_id = ? AND status = 'draft' LIMIT 1`,
      [req.session.user.id]
    );
    const activeDraft = activeDrafts.length > 0 ? activeDrafts[0] : null;

    let items = [];
    if (activeDraft) {
      [items] = await db.query(`SELECT * FROM detail_draft WHERE draft_id = ?`, [activeDraft.id]);
    }

    res.render('kepala_lab/history', {
      title: 'Riwayat Pengadaan',
      activePath: '/kepala-lab/history',
      drafts,
      activeDraft,
      items
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});


// ─── ROLE: KETUA PROGRAM STUDI ─────────────────────────────────────────────────
router.get('/ketua-prodi/review', requireAuth, requireRole('ketua_prodi'), async (req, res) => {
  try {
    const [drafts] = await db.query(`
      SELECT d.*, u.nama AS pengaju,
             (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id) AS total_items,
             (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id AND status_item='pending') AS pending_items
      FROM draft_pengadaan d
      JOIN users u ON d.user_id = u.id
      WHERE d.status IN ('submitted', 'reviewed') AND d.ketua_prodi_id = ?
      ORDER BY d.id DESC
    `, [req.session.user.id]);
    res.render('ketua_prodi/review', {
      title: 'Review Draf Pengadaan',
      activePath: '/ketua-prodi/review',
      drafts
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.get('/ketua-prodi/review/:id', requireAuth, requireRole('ketua_prodi'), async (req, res) => {
  try {
    const [[draft]] = await db.query(
      `SELECT d.*, u.nama AS pengaju FROM draft_pengadaan d JOIN users u ON d.user_id = u.id WHERE d.id = ?`,
      [req.params.id]
    );
    const [items] = await db.query(
      `SELECT * FROM detail_draft WHERE draft_id = ?`,
      [req.params.id]
    );
    const activePath = ['finalized', 'rejected'].includes(draft.status) ? '/ketua-prodi/history' : '/ketua-prodi/review';
    res.render('ketua_prodi/detail', {
      title: `Detail Review Draf #${draft.id}`,
      activePath,
      draft,
      items
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/ketua-prodi/review/:id/process', requireAuth, requireRole('ketua_prodi'), async (req, res) => {
  try {
    const { action, alasan_penolakan } = req.body;
    if (action === 'approve') {
      await db.query(
        `UPDATE draft_pengadaan SET status = 'finalized', alasan_penolakan = ? WHERE id = ?`,
        [alasan_penolakan || '', req.params.id]
      );
      // Set all items in the draft to approved
      await db.query(
        `UPDATE detail_draft SET status_item = 'approved' WHERE draft_id = ?`,
        [req.params.id]
      );
    } else if (action === 'reject') {
      await db.query(
        `UPDATE draft_pengadaan SET status = 'rejected', alasan_penolakan = ? WHERE id = ?`,
        [alasan_penolakan || '', req.params.id]
      );
      // Set all items in the draft to rejected
      await db.query(
        `UPDATE detail_draft SET status_item = 'rejected' WHERE draft_id = ?`,
        [req.params.id]
      );
    }
    res.redirect('/ketua-prodi/history');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.get('/ketua-prodi/history', requireAuth, requireRole('ketua_prodi'), async (req, res) => {
  try {
    const [drafts] = await db.query(`
      SELECT d.*, u.nama AS pengaju,
             (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id) AS total_items
      FROM draft_pengadaan d
      JOIN users u ON d.user_id = u.id
      WHERE d.status IN ('finalized', 'rejected') AND d.ketua_prodi_id = ?
      ORDER BY d.id DESC
    `, [req.session.user.id]);
    res.render('ketua_prodi/history', {
      title: 'Riwayat Draf Pengadaan',
      activePath: '/ketua-prodi/history',
      drafts
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});


// ─── ROLE: STAF ADMINISTRASI ──────────────────────────────────────────────────
router.get('/staf-admin/drafts', requireAuth, requireRole('staf_admin'), async (req, res) => {
  try {
    const [drafts] = await db.query(`
      SELECT d.*, u.nama AS pengaju,
             (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id AND status_item='approved') AS approved_items
      FROM draft_pengadaan d
      JOIN users u ON d.user_id = u.id
      WHERE d.status = 'finalized'
      ORDER BY d.id DESC
    `);
    res.render('staf_admin/drafts', {
      title: 'Draf Pengadaan Disetujui',
      activePath: '/staf-admin/drafts',
      drafts
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.get('/staf-admin/inventaris', requireAuth, requireRole('staf_admin'), async (req, res) => {
  try {
    // List item inventaris yang sudah approved dan finalized tapi belum di-receive sepenuhnya
    const [pendingItems] = await db.query(`
      SELECT dd.*, dp.tahun, u.nama AS pengaju
      FROM detail_draft dd
      JOIN draft_pengadaan dp ON dd.draft_id = dp.id
      JOIN users u ON dp.user_id = u.id
      LEFT JOIN inventaris iv ON iv.detail_draft_id = dd.id
      WHERE dp.status = 'finalized' 
        AND dd.status_item = 'approved' 
        AND dd.tipe_barang = 'inventaris'
        AND iv.id IS NULL
    `);

    // List item inventaris yang sudah berhasil diterima
    const [receivedItems] = await db.query(`
      SELECT iv.*, dd.nama_barang, dd.harga_satuan, r.nama_ruangan
      FROM inventaris iv
      JOIN detail_draft dd ON iv.detail_draft_id = dd.id
      LEFT JOIN ruangan r ON iv.ruangan_id = r.id
      ORDER BY iv.id DESC
    `);

    const [ruangan] = await db.query(`SELECT * FROM ruangan`);

    res.render('staf_admin/inventaris', {
      title: 'Update & Labeling Inventaris',
      activePath: '/staf-admin/inventaris',
      pendingItems,
      receivedItems,
      ruangan
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/staf-admin/inventaris/receive/:itemId', requireAuth, requireRole('staf_admin'), async (req, res) => {
  try {
    const { nomor_label, ruangan_id, tanggal_terima, kondisi } = req.body;

    // Upload foto qr mockup (disimpan string text aja)
    const qrMock = `QR-${nomor_label}.png`;

    await db.query(
      `INSERT INTO inventaris (ruangan_id, detail_draft_id, nama_barang, nomor_label, kondisi, tanggal_terima, barcode_qr)
       SELECT ?, ?, nama_barang, ?, ?, ?, ? FROM detail_draft WHERE id = ?`,
      [ruangan_id, req.params.itemId, nomor_label, kondisi || 'baik', tanggal_terima || new Date(), qrMock, req.params.itemId]
    );

    res.redirect('/staf-admin/inventaris');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

// ─── ROLE: STAF LABORATORIUM ───────────────────────────────────────────────────
router.get('/staf-lab/bhp', requireAuth, requireRole('staf_lab'), async (req, res) => {
  try {
    const [bhpList] = await db.query(`
      SELECT b.*, r.nama_ruangan 
      FROM bhp b
      LEFT JOIN ruangan r ON b.ruangan_id = r.id
      ORDER BY b.id DESC
    `);
    const [ruangan] = await db.query(`SELECT * FROM ruangan`);

    res.render('staf_lab/bhp', {
      title: 'Kelola Stok BHP',
      activePath: '/staf-lab/bhp',
      bhpList,
      ruangan
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/staf-lab/bhp/create', requireAuth, requireRole('staf_lab'), async (req, res) => {
  try {
    const { nama_bhp, ruangan_id, stok, satuan, kondisi } = req.body;
    await db.query(
      `INSERT INTO bhp (nama_bhp, ruangan_id, stok, satuan, kondisi) VALUES (?, ?, ?, ?, ?)`,
      [nama_bhp, ruangan_id, stok || 0, satuan || 'pcs', kondisi || 'baik']
    );
    res.redirect('/staf-lab/bhp');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/staf-lab/bhp/update-stock/:id', requireAuth, requireRole('staf_lab'), async (req, res) => {
  try {
    const { stok, kondisi } = req.body;
    await db.query(
      `UPDATE bhp SET stok = ?, kondisi = ? WHERE id = ?`,
      [stok, kondisi || 'baik', req.params.id]
    );
    res.redirect('/staf-lab/bhp');
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.get('/staf-lab/maintenance', requireAuth, requireRole('staf_lab'), async (req, res) => {
  try {
    const [logs] = await db.query(`
      SELECT m.*, i.nomor_label, u.nama AS petugas, b.nama_bhp
      FROM maintenance_log m
      JOIN inventaris i ON m.inventaris_id = i.id
      JOIN users u ON m.user_id = u.id
      LEFT JOIN bhp b ON m.bhp_id_used = b.id
      ORDER BY m.id DESC
    `);

    const [inventaris] = await db.query(`
      SELECT iv.*, dd.nama_barang 
      FROM inventaris iv
      JOIN detail_draft dd ON iv.detail_draft_id = dd.id
    `);

    const [bhpList] = await db.query(`SELECT id, nama_bhp, stok FROM bhp WHERE stok > 0`);

    res.render('staf_lab/maintenance', {
      title: 'Log Maintenance & Update Kondisi',
      activePath: '/staf-lab/maintenance',
      logs,
      inventaris,
      bhpList
    });
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

router.post('/staf-lab/maintenance/create', requireAuth, requireRole('staf_lab'), async (req, res) => {
  try {
    const { inventaris_id, bhp_id_used, qty_bhp_used, deskripsi, status_akhir } = req.body;
    const finalBhpId = bhp_id_used ? parseInt(bhp_id_used) : null;
    const finalQty = qty_bhp_used ? parseInt(qty_bhp_used) : 0;

    // Mulai transaction
    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
      // 1. Simpan log maintenance
      await connection.query(
        `INSERT INTO maintenance_log (inventaris_id, user_id, bhp_id_used, qty_bhp_used, deskripsi, tanggal_maintenance)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [inventaris_id, req.session.user.id, finalBhpId, finalBhpId ? finalQty : 0, deskripsi || '', new Date()]
      );

      // 2. Update status akhir kondisi barang inventaris
      await connection.query(
        `UPDATE inventaris SET kondisi = ? WHERE id = ?`,
        [status_akhir || 'baik', inventaris_id]
      );

      // 3. Potong stok BHP jika ada yang digunakan
      if (finalBhpId && finalQty > 0) {
        await connection.query(
          `UPDATE bhp SET stok = GREATEST(0, stok - ?) WHERE id = ?`,
          [finalQty, finalBhpId]
        );
      }

      await connection.commit();
      connection.release();
      res.redirect('/staf-lab/maintenance');
    } catch (transactionErr) {
      await connection.rollback();
      connection.release();
      throw transactionErr;
    }
  } catch (err) {
    console.error(err);
    res.status(500).send('Database error: ' + err.message);
  }
});

module.exports = router;
