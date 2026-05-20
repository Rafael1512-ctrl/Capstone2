const express = require('express');
const router = express.Router();

// Controllers
const authController = require('../controllers/authController');
const dashboardController = require('../controllers/dashboardController');
const adminController = require('../controllers/adminController');
const kepalaLabController = require('../controllers/kepalaLabController');
const ketuaProdiController = require('../controllers/ketuaProdiController');
const stafAdminController = require('../controllers/stafAdminController');
const stafLabController = require('../controllers/stafLabController');

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
router.get('/signin', authController.showSignIn);
router.post('/signin', authController.handleSignIn);
router.get('/signout', authController.handleSignOut);

// ─── DASHBOARD ─────────────────────────────────────────────────────────────────
router.get('/', requireAuth, dashboardController.showDashboard);

// ─── GENERAL INVENTORY ─────────────────────────────────────────────────────────
router.get('/inventory', requireAuth, dashboardController.showInventory);
router.get('/create-product', requireAuth, dashboardController.showCreateProduct);
router.post('/create-product', requireAuth, dashboardController.handleCreateProduct);

// ─── GENERAL REPORTS ───────────────────────────────────────────────────────────
router.get('/reports', requireAuth, dashboardController.showReports);

// ─── GENERAL DOCS ──────────────────────────────────────────────────────────────
router.get('/docs', requireAuth, dashboardController.showDocs);

// ─── ROLE: ADMINISTRATOR ───────────────────────────────────────────────────────
router.get('/admin/users', requireAuth, requireRole('admin'), adminController.showUsers);
router.post('/admin/users/create', requireAuth, requireRole('admin'), adminController.createUser);
router.post('/admin/users/delete/:id', requireAuth, requireRole('admin'), adminController.deleteUser);
router.post('/admin/users/edit/:id', requireAuth, requireRole('admin'), adminController.editUser);

router.get('/admin/ruangan', requireAuth, requireRole('admin'), adminController.showRuangan);
router.post('/admin/ruangan/create', requireAuth, requireRole('admin'), adminController.createRuangan);
router.post('/admin/ruangan/delete/:id', requireAuth, requireRole('admin'), adminController.deleteRuangan);
router.post('/admin/ruangan/edit/:id', requireAuth, requireRole('admin'), adminController.editRuangan);

// ─── ROLE: KEPALA LABORATORIUM ─────────────────────────────────────────────────
router.get('/kepala-lab/pengadaan', requireAuth, requireRole('kepala_lab'), kepalaLabController.showPengadaan);
router.post('/kepala-lab/pengadaan/create-draft', requireAuth, requireRole('kepala_lab'), kepalaLabController.createDraft);
router.post('/kepala-lab/pengadaan/update-draft/:id', requireAuth, requireRole('kepala_lab'), kepalaLabController.updateDraft);
router.post('/kepala-lab/pengadaan/delete-draft/:id', requireAuth, requireRole('kepala_lab'), kepalaLabController.deleteDraft);
router.post('/kepala-lab/pengadaan/add-item', requireAuth, requireRole('kepala_lab'), kepalaLabController.addItem);
router.post('/kepala-lab/pengadaan/delete-item/:id', requireAuth, requireRole('kepala_lab'), kepalaLabController.deleteItem);
router.post('/kepala-lab/pengadaan/submit/:id', requireAuth, requireRole('kepala_lab'), kepalaLabController.submitDraft);
router.get('/kepala-lab/history', requireAuth, requireRole('kepala_lab'), kepalaLabController.showHistory);

// ─── ROLE: KETUA PROGRAM STUDI ─────────────────────────────────────────────────
router.get('/ketua-prodi/review', requireAuth, requireRole('ketua_prodi'), ketuaProdiController.showReview);
router.get('/ketua-prodi/review/:id', requireAuth, requireRole('ketua_prodi'), ketuaProdiController.showReviewDetail);
router.post('/ketua-prodi/review/:id/process', requireAuth, requireRole('ketua_prodi'), ketuaProdiController.processDraft);
router.get('/ketua-prodi/history', requireAuth, requireRole('ketua_prodi'), ketuaProdiController.showHistory);

// ─── ROLE: STAF ADMINISTRASI ──────────────────────────────────────────────────
router.get('/staf-admin/drafts', requireAuth, requireRole('staf_admin'), stafAdminController.showDrafts);
router.get('/staf-admin/inventaris', requireAuth, requireRole('staf_admin'), stafAdminController.showInventaris);
router.post('/staf-admin/inventaris/receive/:itemId', requireAuth, requireRole('staf_admin'), stafAdminController.receiveItem);

// ─── ROLE: STAF LABORATORIUM ───────────────────────────────────────────────────
router.get('/staf-lab/bhp', requireAuth, requireRole('staf_lab'), stafLabController.showBHP);
router.post('/staf-lab/bhp/create', requireAuth, requireRole('staf_lab'), stafLabController.createBHP);
router.post('/staf-lab/bhp/update-stock/:id', requireAuth, requireRole('staf_lab'), stafLabController.updateBHPStock);

router.get('/staf-lab/maintenance', requireAuth, requireRole('staf_lab'), stafLabController.showMaintenance);
router.post('/staf-lab/maintenance/create', requireAuth, requireRole('staf_lab'), stafLabController.createMaintenance);

module.exports = router;
