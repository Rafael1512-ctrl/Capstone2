const db = require('../config/db');
const bcrypt = require('bcryptjs');

class User {
  // Get user by email with role
  static async getByEmail(email) {
    const [rows] = await db.query(
      `SELECT u.*, r.nama AS role 
       FROM users u
       JOIN user_roles ur ON ur.user_id = u.id
       JOIN roles r ON ur.role_id = r.id
       WHERE u.email = ?`,
      [email]
    );
    return rows.length > 0 ? rows[0] : null;
  }

  // Get all users with roles
  static async getAll() {
    const [rows] = await db.query(
      `SELECT u.id, u.nama, u.email, r.nama AS role, r.id AS role_id
       FROM users u
       LEFT JOIN user_roles ur ON ur.user_id = u.id
       LEFT JOIN roles r ON ur.role_id = r.id
       ORDER BY u.id DESC`
    );
    return rows;
  }

  // Get user by ID
  static async getById(id) {
    const [rows] = await db.query(
      `SELECT u.*, r.nama AS role FROM users u
       LEFT JOIN user_roles ur ON ur.user_id = u.id
       LEFT JOIN roles r ON ur.role_id = r.id
       WHERE u.id = ?`,
      [id]
    );
    return rows.length > 0 ? rows[0] : null;
  }

  // Create new user with role
  static async create(nama, email, password, roleId) {
    const hashedPassword = bcrypt.hashSync(password || 'password', 10);
    const [result] = await db.query(
      `INSERT INTO users (nama, email, password) VALUES (?, ?, ?)`,
      [nama, email, hashedPassword]
    );
    await db.query(
      `INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)`,
      [result.insertId, roleId]
    );
    return result.insertId;
  }

  // Update user
  static async update(id, nama, email, password, roleId) {
    if (password && password.trim() !== '') {
      const hashedPassword = bcrypt.hashSync(password, 10);
      await db.query(
        `UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?`,
        [nama, email, hashedPassword, id]
      );
    } else {
      await db.query(
        `UPDATE users SET nama = ?, email = ? WHERE id = ?`,
        [nama, email, id]
      );
    }
    await db.query(`DELETE FROM user_roles WHERE user_id = ?`, [id]);
    await db.query(
      `INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)`,
      [id, roleId]
    );
  }

  // Delete user
  static async delete(id) {
    await db.query(`DELETE FROM user_roles WHERE user_id = ?`, [id]);
    await db.query(`DELETE FROM users WHERE id = ?`, [id]);
  }

  // Verify password
  static verifyPassword(password, hashedPassword) {
    return bcrypt.compareSync(password, hashedPassword);
  }

  // Get all roles
  static async getAllRoles() {
    const [rows] = await db.query(`SELECT * FROM roles`);
    return rows;
  }

  // Get ketua prodi list
  static async getKetuaProdiList() {
    const [rows] = await db.query(
      `SELECT u.id, u.nama FROM users u 
       JOIN user_roles ur ON u.id = ur.user_id 
       JOIN roles r ON ur.role_id = r.id 
       WHERE r.nama = 'ketua_prodi'`
    );
    return rows;
  }
}

module.exports = User;
