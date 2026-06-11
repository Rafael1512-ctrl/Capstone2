const db = require("../config/db");
const bcrypt = require("bcryptjs");

const KETUA_PRODI_ROLE = "ketua_prodi";

class User {
  // Get user by email with role (active users only)
  static async getByEmail(email) {
    const [rows] = await db.query(
      `SELECT u.*, r.nama AS role 
       FROM users u
       JOIN user_roles ur ON ur.user_id = u.id
       JOIN roles r ON ur.role_id = r.id
       WHERE u.email = ? AND u.deleted_at IS NULL`,
      [email],
    );
    return rows.length > 0 ? rows[0] : null;
  }

  // Get all active users with roles
  static async getAll() {
    const [rows] = await db.query(
      `SELECT u.id, u.nama, u.email, u.email_verified_at, r.nama AS role, r.id AS role_id
       FROM users u
       LEFT JOIN user_roles ur ON ur.user_id = u.id
       LEFT JOIN roles r ON ur.role_id = r.id
       WHERE u.deleted_at IS NULL
       ORDER BY u.id DESC`,
    );
    return rows;
  }

  // Get user by ID (includes soft-deleted for internal checks)
  static async getById(id) {
    const [rows] = await db.query(
      `SELECT u.*, r.nama AS role, r.id AS role_id FROM users u
       LEFT JOIN user_roles ur ON ur.user_id = u.id
       LEFT JOIN roles r ON ur.role_id = r.id
       WHERE u.id = ?`,
      [id],
    );
    return rows.length > 0 ? rows[0] : null;
  }

  // Get active user by ID
  static async getActiveById(id) {
    const [rows] = await db.query(
      `SELECT u.*, r.nama AS role, r.id AS role_id FROM users u
       LEFT JOIN user_roles ur ON ur.user_id = u.id
       LEFT JOIN roles r ON ur.role_id = r.id
       WHERE u.id = ? AND u.deleted_at IS NULL`,
      [id],
    );
    return rows.length > 0 ? rows[0] : null;
  }

  static async getRoleNameById(roleId) {
    const [rows] = await db.query(`SELECT nama FROM roles WHERE id = ?`, [
      roleId,
    ]);
    return rows.length > 0 ? rows[0].nama : null;
  }

  static async countActiveKetuaProdi(excludeUserId = null) {
    let sql = `SELECT COUNT(*) AS cnt FROM users u
      JOIN user_roles ur ON u.id = ur.user_id
      JOIN roles r ON ur.role_id = r.id
      WHERE r.nama = ? AND u.deleted_at IS NULL`;
    const params = [KETUA_PRODI_ROLE];

    if (excludeUserId) {
      sql += ` AND u.id != ?`;
      params.push(excludeUserId);
    }

    const [rows] = await db.query(sql, params);
    return rows[0].cnt;
  }

  static async isKetuaProdiRole(roleId) {
    const roleName = await User.getRoleNameById(roleId);
    return roleName === KETUA_PRODI_ROLE;
  }

  // Create new user with role (email unverified by default)
  static async create(nama, email, password, roleId) {
    const hashedPassword = bcrypt.hashSync(password || "password", 10);
    const [result] = await db.query(
      `INSERT INTO users (nama, email, password, email_verified_at) VALUES (?, ?, ?, NULL)`,
      [nama, email, hashedPassword],
    );
    await db.query(`INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)`, [
      result.insertId,
      roleId,
    ]);
    return result.insertId;
  }

  // Update user
  static async update(id, nama, email, password, roleId) {
    if (password && password.trim() !== "") {
      const hashedPassword = bcrypt.hashSync(password, 10);
      await db.query(
        `UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?`,
        [nama, email, hashedPassword, id],
      );
    } else {
      await db.query(`UPDATE users SET nama = ?, email = ? WHERE id = ?`, [
        nama,
        email,
        id,
      ]);
    }
    await db.query(`DELETE FROM user_roles WHERE user_id = ?`, [id]);
    await db.query(`INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)`, [
      id,
      roleId,
    ]);
  }

  // Hard delete user (removes from DB entirely so email can be re-registered)
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

  // Get ketua prodi list (active only)
  static async getKetuaProdiList() {
    const [rows] = await db.query(
      `SELECT u.id, u.nama FROM users u 
       JOIN user_roles ur ON u.id = ur.user_id 
       JOIN roles r ON ur.role_id = r.id 
       WHERE r.nama = ? AND u.deleted_at IS NULL`,
      [KETUA_PRODI_ROLE],
    );
    return rows;
  }

  // Get default ketua prodi (first active one)
  static async getKetuaProdiDefault() {
    const [rows] = await db.query(
      `SELECT u.id, u.nama FROM users u 
       JOIN user_roles ur ON u.id = ur.user_id 
       JOIN roles r ON ur.role_id = r.id 
       WHERE r.nama = ? AND u.deleted_at IS NULL
       LIMIT 1`,
      [KETUA_PRODI_ROLE],
    );
    return rows.length > 0 ? rows[0] : null;
  }

  // Update profile (name, email, optional password)
  static async updateProfile(id, nama, email, password) {
    if (password && password.trim() !== "") {
      const hashedPassword = bcrypt.hashSync(password, 10);
      await db.query(
        `UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?`,
        [nama, email, hashedPassword, id],
      );
    } else {
      await db.query(`UPDATE users SET nama = ?, email = ? WHERE id = ?`, [
        nama,
        email,
        id,
      ]);
    }
  }
}

module.exports = User;
