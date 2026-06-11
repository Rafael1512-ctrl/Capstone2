const jwt = require('jsonwebtoken');
const User = require('../models/User');
const JWT_SECRET = 'super-secret-key-capstone-inventory-lab';

function authenticateToken(req, res, next) {
  let token = null;

  // 1. Check Authorization header
  const authHeader = req.headers['authorization'];
  if (authHeader && authHeader.startsWith('Bearer ')) {
    token = authHeader.split(' ')[1];
  }

  // 2. Check cookies as fallback
  if (!token && req.cookies && req.cookies.token) {
    token = req.cookies.token;
  }

  if (!token) {
    return res.status(401).json({ error: 'Unauthorized: No token provided' });
  }

  jwt.verify(token, JWT_SECRET, async (err, decoded) => {
    if (err) {
      return res.status(401).json({ error: 'Unauthorized: Invalid or expired token' });
    }

    try {
      const user = await User.getActiveById(decoded.id);
      if (!user) {
        return res.status(401).json({ error: 'Unauthorized: Akun tidak aktif.' });
      }

      req.user = {
        id: user.id,
        nama: user.nama,
        email: user.email,
        role: user.role,
      };
      next();
    } catch (verifyErr) {
      console.error(verifyErr);
      return res.status(500).json({ error: 'Server error: ' + verifyErr.message });
    }
  });
}

function requireRole(role) {
  return (req, res, next) => {
    if (!req.user || req.user.role !== role) {
      return res.status(403).json({ error: 'Forbidden: Access denied' });
    }
    next();
  };
}

module.exports = {
  authenticateToken,
  requireRole,
  JWT_SECRET
};
