const User = require('../models/User');
const jwt = require('jsonwebtoken');
const { JWT_SECRET } = require('../middleware/authMiddleware');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const crypto = require('crypto');
const nodemailer = require('nodemailer');
const bcrypt = require('bcryptjs');

// Configure multer storage and upload
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    const dir = path.join(__dirname, '../public/uploads/avatars');
    fs.mkdirSync(dir, { recursive: true });
    cb(null, dir);
  },
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname).toLowerCase();
    const filename = `${req.user.id}_${Date.now()}${ext}`;
    cb(null, filename);
  }
});

const upload = multer({
  storage,
  limits: { fileSize: 2 * 1024 * 1024 }, // 2MB
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png|webp/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    if (extname && mimetype) {
      return cb(null, true);
    }
    cb(new Error('Hanya diperbolehkan mengunggah berkas gambar (jpg, jpeg, png, webp)!'));
  }
}).single('avatar');

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

      if (!user.email_verified_at) {
        return res.status(403).json({
          error: 'Email belum diverifikasi. Silakan cek inbox Anda atau hubungi administrator untuk mengirim ulang email verifikasi.',
          code: 'EMAIL_NOT_VERIFIED',
        });
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

      const updatedUser = await User.getActiveById(userId);
      if (!updatedUser) {
        return res.status(403).json({ error: 'Akun tidak aktif.' });
      }

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

  // GET /api/user/profile
  static async getUserProfile(req, res) {
    try {
      const user = await User.getActiveById(req.user.id);
      if (!user) {
        return res.status(404).json({ error: 'Pengguna tidak ditemukan.' });
      }

      res.json({
        id: user.id,
        name: user.nama,
        email: user.email,
        phone: user.phone || null,
        position: user.position || null,
        avatar_url: user.avatar ? `${req.protocol}://${req.get('host')}/uploads/avatars/${user.avatar}` : null,
        role: user.role
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Server error: ' + err.message });
    }
  }

  // PUT /api/user/profile
  static async updateUserProfile(req, res) {
    try {
      const { name, phone, position } = req.body;
      const userId = req.user.id;

      if (!name || name.trim() === '') {
        return res.status(400).json({ error: 'Nama lengkap harus diisi.' });
      }

      if (phone && phone.length > 15) {
        return res.status(400).json({ error: 'Nomor telepon maksimal 15 karakter.' });
      }

      await User.updateUserProfile(userId, name, phone, position);

      const updatedUser = await User.getActiveById(userId);
      if (!updatedUser) {
        return res.status(404).json({ error: 'Pengguna tidak ditemukan.' });
      }

      res.json({
        success: true,
        message: 'Profil berhasil diperbarui.',
        user: {
          id: updatedUser.id,
          name: updatedUser.nama,
          email: updatedUser.email,
          phone: updatedUser.phone,
          position: updatedUser.position,
          avatar_url: updatedUser.avatar ? `${req.protocol}://${req.get('host')}/uploads/avatars/${updatedUser.avatar}` : null,
          role: updatedUser.role
        }
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Server error: ' + err.message });
    }
  }

  // POST /api/user/avatar
  static async updateUserAvatar(req, res) {
    upload(req, res, async (err) => {
      if (err instanceof multer.MulterError) {
        if (err.code === 'LIMIT_FILE_SIZE') {
          return res.status(400).json({ error: 'Ukuran berkas terlalu besar. Maksimal 2MB.' });
        }
        return res.status(400).json({ error: err.message });
      } else if (err) {
        return res.status(400).json({ error: err.message });
      }

      if (!req.file) {
        return res.status(400).json({ error: 'Tidak ada berkas yang diunggah.' });
      }

      try {
        const userId = req.user.id;
        const filename = req.file.filename;

        // Delete old avatar file if exists
        const user = await User.getActiveById(userId);
        if (user && user.avatar) {
          const oldPath = path.join(__dirname, '../public/uploads/avatars', user.avatar);
          if (fs.existsSync(oldPath)) {
            try {
              fs.unlinkSync(oldPath);
            } catch (fsErr) {
              console.error('Failed to delete old avatar:', fsErr);
            }
          }
        }

        await User.updateAvatar(userId, filename);

        const avatarUrl = `${req.protocol}://${req.get('host')}/uploads/avatars/${filename}`;
        res.json({
          success: true,
          message: 'Foto profil berhasil diperbarui.',
          avatar_url: avatarUrl
        });
      } catch (dbErr) {
        console.error(dbErr);
        res.status(500).json({ error: 'Database error: ' + dbErr.message });
      }
    });
  }

  // POST /api/auth/forgot-password
  static async forgotPassword(req, res) {
    try {
      const { email } = req.body;
      if (!email) {
        return res.status(400).json({ error: 'Email wajib diisi.' });
      }

      // Security requirement: Always return success message even if not found
      const successMessage = 'Link reset password telah dikirim ke email Anda.';

      const user = await User.getByEmail(email);
      if (!user) {
        return res.json({ success: true, message: successMessage });
      }

      // Generate a secure random token
      const token = crypto.randomBytes(32).toString('hex');
      const expires = new Date();
      expires.setHours(expires.getHours() + 1); // 1 hour from now

      // Save token to DB
      await User.saveResetToken(user.id, token, expires);

      // Create nodemailer transporter
      const transporter = nodemailer.createTransport({
        host: process.env.MAIL_HOST || 'smtp.gmail.com',
        port: parseInt(process.env.MAIL_PORT || '587'),
        secure: parseInt(process.env.MAIL_PORT || '587') === 465,
        auth: {
          user: process.env.MAIL_USER || 'rafaeladiputra772@gmail.com',
          pass: process.env.MAIL_PASS || 'slfe uwuw jfaw vwnm'
        }
      });

      const resetLink = `http://127.0.0.1:8000/reset-password?token=${token}&email=${encodeURIComponent(email)}`;

      // Branded HTML Template
      const mailOptions = {
        from: `"${process.env.MAIL_FROM_NAME || 'InLab Inventory Lab'}" <${process.env.MAIL_FROM || process.env.MAIL_USER || 'rafaeladiputra772@gmail.com'}>`,
        to: email,
        subject: 'Reset Password - InLab Inventory Lab',
        html: `
          <div style="font-family: 'Plus Jakarta Sans', 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;">
            <div style="max-width: 580px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
              
              <!-- Header -->
              <div style="background-color: #0f172a; padding: 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">
                  <span style="color: #10b981;">In</span>Lab
                </h1>
                <p style="color: #94a3b8; margin: 5px 0 0 0; font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Inventory Lab</p>
              </div>

              <!-- Body -->
              <div style="padding: 40px 30px;">
                <h2 style="color: #0f172a; font-size: 20px; font-weight: 700; margin-top: 0; margin-bottom: 20px;">Permintaan Reset Password</h2>
                <p style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 30px;">
                  Halo,<br><br>
                  Kami menerima permintaan untuk mereset kata sandi akun Anda di <strong>InLab Inventory Lab</strong>. Silakan klik tombol di bawah ini untuk mereset kata sandi Anda:
                </p>
                
                <!-- Button -->
                <div style="text-align: center; margin-bottom: 35px;">
                  <a href="${resetLink}" style="display: inline-block; background-color: #4f46e5; color: #ffffff; font-weight: 700; font-size: 15px; padding: 14px 32px; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25); transition: background-color 0.2s;">
                    Reset Password Saya
                  </a>
                </div>

                <p style="font-size: 13px; line-height: 1.6; color: #64748b; margin-bottom: 20px;">
                  Link tautan di atas hanya akan berlaku selama <strong>1 jam</strong>. Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini dan kata sandi Anda tidak akan berubah.
                </p>

                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 30px 0;">

                <p style="font-size: 12px; line-height: 1.5; color: #94a3b8; margin-bottom: 0;">
                  Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:<br>
                  <a href="${resetLink}" style="color: #4f46e5; word-break: break-all;">${resetLink}</a>
                </p>
              </div>

              <!-- Footer -->
              <div style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #f1f5f9;">
                <p style="color: #94a3b8; font-size: 11px; margin: 0;">&copy; 2026 InLab — Sistem Informasi Inventaris &amp; Pengadaan Laboratorium.</p>
              </div>

            </div>
          </div>
        `
      };

      // Send email
      await transporter.sendMail(mailOptions);

      res.json({ success: true, message: successMessage });
    } catch (err) {
      console.error('Forgot password error:', err);
      res.status(500).json({ error: 'Server error: ' + err.message });
    }
  }

  // POST /api/auth/reset-password
  static async resetPassword(req, res) {
    try {
      const { email, token, password, password_confirmation } = req.body;

      if (!email || !token || !password || !password_confirmation) {
        return res.status(400).json({ error: 'Semua bidang wajib diisi.' });
      }

      if (password !== password_confirmation) {
        return res.status(400).json({ error: 'Konfirmasi password tidak cocok.' });
      }

      if (password.length < 8) {
        return res.status(400).json({ error: 'Password minimal harus 8 karakter.' });
      }

      // Verify token
      const user = await User.getUserByResetToken(email, token);
      if (!user) {
        return res.status(400).json({ error: 'Token reset password tidak valid atau sudah kedaluwarsa.' });
      }

      // Hash the new password
      const hashedPassword = bcrypt.hashSync(password, 10);

      // Update password and clear token
      await User.resetUserPassword(user.id, hashedPassword);

      res.json({ success: true, message: 'Password Anda berhasil diubah.' });
    } catch (err) {
      console.error('Reset password error:', err);
      res.status(500).json({ error: 'Server error: ' + err.message });
    }
  }
}

module.exports = AuthController;
