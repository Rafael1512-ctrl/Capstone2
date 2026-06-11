const User = require('../models/User');
const Ruangan = require('../models/Ruangan');

const KETUA_PRODI_ERROR =
  'Ketua Prodi aktif sudah ada. Hapus atau nonaktifkan yang lama terlebih dahulu.';

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

      if (await User.isKetuaProdiRole(role_id)) {
        const count = await User.countActiveKetuaProdi();
        if (count > 0) {
          return res.status(422).json({ error: KETUA_PRODI_ERROR });
        }
      }

      const userId = await User.create(nama, email, password, role_id);
      res.json({
        success: true,
        message: 'User created successfully',
        user_id: userId,
      });
    } catch (err) {
      console.error(err);
      if (err.code === 'ER_DUP_ENTRY') {
        return res.status(422).json({ error: 'Email sudah terdaftar.' });
      }
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/admin/users/delete/:id
  static async deleteUser(req, res) {
    try {
      const user = await User.getActiveById(req.params.id);
      if (!user) {
        return res.status(404).json({ error: 'Pengguna tidak ditemukan.' });
      }

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
      const userId = req.params.id;

      const user = await User.getActiveById(userId);
      if (!user) {
        return res.status(404).json({ error: 'Pengguna tidak ditemukan.' });
      }

      if (await User.isKetuaProdiRole(role_id)) {
        const count = await User.countActiveKetuaProdi(userId);
        if (count > 0) {
          return res.status(422).json({ error: KETUA_PRODI_ERROR });
        }
      }

      await User.update(userId, nama, email, password, role_id);
      res.json({ success: true, message: 'User updated successfully' });
    } catch (err) {
      console.error(err);
      if (err.code === 'ER_DUP_ENTRY') {
        return res.status(422).json({ error: 'Email sudah terdaftar.' });
      }
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
