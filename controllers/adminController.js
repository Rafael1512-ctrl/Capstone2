const User = require('../models/User');
const Ruangan = require('../models/Ruangan');

class AdminController {
  // GET /api/admin/users
  static async showUsers(req, res) {
    try {
      const users = await User.getAll();
      const roles = await User.getAllRoles();
      res.json({
        title: 'Kelola Pengguna',
        activePath: '/admin/users',
        users,
        roles,
        hasUsers: users.length > 0,
        userCount: users.length,
        hasRoles: roles.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/admin/users/create
  static async createUser(req, res) {
    try {
      const { nama, email, password, role_id } = req.body;
      await User.create(nama, email, password, role_id);
      res.json({ success: true, message: 'User created successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/admin/users/delete/:id
  static async deleteUser(req, res) {
    try {
      await User.delete(req.params.id);
      res.json({ success: true, message: 'User deleted successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/admin/users/edit/:id
  static async editUser(req, res) {
    try {
      const { nama, email, password, role_id } = req.body;
      await User.update(req.params.id, nama, email, password, role_id);
      res.json({ success: true, message: 'User updated successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/admin/ruangan
  static async showRuangan(req, res) {
    try {
      const ruangan = await Ruangan.getAll();
      res.json({
        title: 'Kelola Ruangan',
        activePath: '/admin/ruangan',
        ruangan,
        hasRuangan: ruangan.length > 0,
        ruanganCount: ruangan.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/admin/ruangan/create
  static async createRuangan(req, res) {
    try {
      const { nama_ruangan, kode_ruangan, lokasi } = req.body;
      await Ruangan.create(nama_ruangan, kode_ruangan, lokasi);
      res.json({ success: true, message: 'Ruangan created successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/admin/ruangan/delete/:id
  static async deleteRuangan(req, res) {
    try {
      await Ruangan.delete(req.params.id);
      res.json({ success: true, message: 'Ruangan deleted successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/admin/ruangan/edit/:id
  static async editRuangan(req, res) {
    try {
      const { nama_ruangan, kode_ruangan, lokasi } = req.body;
      await Ruangan.update(req.params.id, nama_ruangan, kode_ruangan, lokasi);
      res.json({ success: true, message: 'Ruangan updated successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }
}

module.exports = AdminController;
