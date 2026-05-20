const User = require('../models/User');

class AuthController {
  // GET /signin
  static async showSignIn(req, res) {
    if (req.session.user) return res.redirect('/');
    res.render('signin', { title: 'Sign In', error: req.query.error });
  }

  // POST /signin
  static async handleSignIn(req, res) {
    try {
      const { email, password } = req.body;
      if (!email || !password) {
        return res.render('signin', { title: 'Sign In', error: 'Email dan password harus diisi.' });
      }

      const user = await User.getByEmail(email);
      if (!user) {
        return res.render('signin', { title: 'Sign In', error: 'Akun tidak ditemukan.' });
      }

      const isMatch = User.verifyPassword(password, user.password);
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
  }

  // GET /signout
  static async handleSignOut(req, res) {
    req.session.destroy();
    res.redirect('/signin');
  }
}

module.exports = AuthController;
