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

// Middlewares
const { authenticateToken, requireRole } = require('../middleware/authMiddleware');

// ─── AUTHENTICATION ROUTES ────────────────────────────────────────────────────
router.post('/auth/login', authController.handleSignIn);
router.post('/auth/logout', authController.handleSignOut);
router.get('/notifications', authenticateToken, dashboardController.showNotifications);
router.post('/profile/update', authenticateToken, authController.updateProfile);

// ─── DASHBOARD ─────────────────────────────────────────────────────────────────
router.get('/', authenticateToken, dashboardController.showDashboard);

// ─── GENERAL INVENTORY ─────────────────────────────────────────────────────────
router.get('/inventory', authenticateToken, dashboardController.showInventory);
router.get('/create-product', authenticateToken, dashboardController.showCreateProduct);
router.post('/create-product', authenticateToken, dashboardController.handleCreateProduct);

// ─── GENERAL REPORTS ───────────────────────────────────────────────────────────
router.get('/reports', authenticateToken, dashboardController.showReports);

// ─── GENERAL DOCS ──────────────────────────────────────────────────────────────
router.get('/docs', authenticateToken, dashboardController.showDocs);
router.get('/maintenance', authenticateToken, dashboardController.showMaintenanceLogs);

// ─── ROLE: ADMINISTRATOR ───────────────────────────────────────────────────────
router.get('/admin/users', authenticateToken, requireRole('admin'), adminController.showUsers);
router.post('/admin/users/create', authenticateToken, requireRole('admin'), adminController.createUser);
router.post('/admin/users/delete/:id', authenticateToken, requireRole('admin'), adminController.deleteUser);
router.post('/admin/users/edit/:id', authenticateToken, requireRole('admin'), adminController.editUser);

router.get('/admin/ruangan', authenticateToken, requireRole('admin'), adminController.showRuangan);
router.post('/admin/ruangan/create', authenticateToken, requireRole('admin'), adminController.createRuangan);
router.post('/admin/ruangan/delete/:id', authenticateToken, requireRole('admin'), adminController.deleteRuangan);
router.post('/admin/ruangan/edit/:id', authenticateToken, requireRole('admin'), adminController.editRuangan);

// ─── ROLE: KEPALA LABORATORIUM ─────────────────────────────────────────────────
router.get('/kepala-lab/pengadaan', authenticateToken, requireRole('kepala_lab'), kepalaLabController.showPengadaan);
router.post('/kepala-lab/pengadaan/create-draft', authenticateToken, requireRole('kepala_lab'), kepalaLabController.createDraft);
router.post('/kepala-lab/pengadaan/update-draft/:id', authenticateToken, requireRole('kepala_lab'), kepalaLabController.updateDraft);
router.post('/kepala-lab/pengadaan/delete-draft/:id', authenticateToken, requireRole('kepala_lab'), kepalaLabController.deleteDraft);
router.post('/kepala-lab/pengadaan/add-item', authenticateToken, requireRole('kepala_lab'), kepalaLabController.addItem);
router.post('/kepala-lab/pengadaan/delete-item/:id', authenticateToken, requireRole('kepala_lab'), kepalaLabController.deleteItem);
router.post('/kepala-lab/pengadaan/submit/:id', authenticateToken, requireRole('kepala_lab'), kepalaLabController.submitDraft);
router.get('/kepala-lab/history', authenticateToken, requireRole('kepala_lab'), kepalaLabController.showHistory);

// ─── ROLE: KETUA PROGRAM STUDI ─────────────────────────────────────────────────
router.get('/ketua-prodi/review', authenticateToken, requireRole('ketua_prodi'), ketuaProdiController.showReview);
router.get('/ketua-prodi/review/:id', authenticateToken, requireRole('ketua_prodi'), ketuaProdiController.showReviewDetail);
router.post('/ketua-prodi/review/:id/process', authenticateToken, requireRole('ketua_prodi'), ketuaProdiController.processDraft);
router.get('/ketua-prodi/history', authenticateToken, requireRole('ketua_prodi'), ketuaProdiController.showHistory);

// ─── ROLE: STAF ADMINISTRASI ──────────────────────────────────────────────────
router.get('/staf-admin/drafts', authenticateToken, requireRole('staf_admin'), stafAdminController.showDrafts);
router.get('/inventaris', authenticateToken, stafAdminController.showInventaris);
router.post('/staf-admin/inventaris/receive/:itemId', authenticateToken, requireRole('staf_admin'), stafAdminController.receiveItem);
router.post('/staf-admin/inventaris/delete/:id', authenticateToken, requireRole('staf_admin'), stafAdminController.deleteInventaris);

// ─── ROLE: STAF LABORATORIUM ───────────────────────────────────────────────────
router.get('/staf-lab/bhp', authenticateToken, requireRole('staf_lab'), stafLabController.showBHP);
router.post('/staf-lab/bhp/create', authenticateToken, requireRole('staf_lab'), stafLabController.createBHP);
router.post('/staf-lab/bhp/update-stock/:id', authenticateToken, requireRole('staf_lab'), stafLabController.updateBHPStock);
router.get('/staf-lab/bhp/mutasi', authenticateToken, requireRole('staf_lab'), stafLabController.showBHPMutasi);

router.get('/staf-lab/maintenance', authenticateToken, requireRole('staf_lab'), stafLabController.showMaintenance);
router.post('/staf-lab/maintenance/create', authenticateToken, requireRole('staf_lab'), stafLabController.createMaintenance);

module.exports = router;
