const User = require('../models/User');
const jwt = require('jsonwebtoken');
const { JWT_SECRET } = require('../middleware/authMiddleware');

class AuthController {
  // POST /api/auth/login
  static async handleSignIn(req, res) {
    try {
      const { email, password } = req.body;
      if (!email || !password) {
        return res.status(400).json({ error: 'Email dan password harus diisi.' });
      }

      const user = await User.getByEmail(email);
      if (!user) {
        return res.status(401).json({ error: 'Akun tidak ditemukan.' });
      }

      const isMatch = User.verifyPassword(password, user.password);
      if (!isMatch) {
        return res.status(401).json({ error: 'Password salah.' });
      }

      const payload = {
        id: user.id,
        nama: user.nama,
        email: user.email,
        role: user.role
      };

      const token = jwt.sign(payload, JWT_SECRET, { expiresIn: '1d' });

      res.json({
        success: true,
        token,
        user: payload
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Server error: ' + err.message });
    }
  }

  // POST /api/auth/logout
  static async handleSignOut(req, res) {
    res.json({ success: true, message: 'Logged out successfully' });
  }

  // POST /api/profile/update
  static async updateProfile(req, res) {
    try {
      const { nama, email, password } = req.body;
      const userId = req.user.id;

      if (!nama || !email) {
        return res.status(400).json({ error: 'Nama dan email harus diisi.' });
      }

      const existingUser = await User.getByEmail(email);
      if (existingUser && existingUser.id !== userId) {
        return res.status(400).json({ error: 'Email sudah digunakan oleh akun lain.' });
      }

      await User.updateProfile(userId, nama, email, password);

      const updatedUser = await User.getById(userId);
      const payload = {
        id: updatedUser.id,
        nama: updatedUser.nama,
        email: updatedUser.email,
        role: updatedUser.role
      };

      const token = jwt.sign(payload, JWT_SECRET, { expiresIn: '1d' });

      res.json({
        success: true,
        message: 'Profil berhasil diperbarui.',
        token,
        user: payload
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Server error: ' + err.message });
    }
  }
}

module.exports = AuthController;
