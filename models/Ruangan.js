const db = require('../config/db');

class Ruangan {
  // Get all ruangan
  static async getAll() {
    const [rows] = await db.query(`SELECT * FROM ruangan ORDER BY id DESC`);
    return rows;
  }

  // Check if kode_ruangan exists
  static async checkKodeExists(kode_ruangan, excludeId = null) {
    let sql = `SELECT id FROM ruangan WHERE kode_ruangan = ?`;
    const params = [kode_ruangan];
    if (excludeId) {
      sql += ` AND id != ?`;
      params.push(excludeId);
    }
    const [rows] = await db.query(sql, params);
    return rows.length > 0;
  }

  // Check if nama_ruangan and lokasi combination exists
  static async checkNameAndLocationExists(nama_ruangan, lokasi, excludeId = null) {
    let sql = `SELECT id FROM ruangan WHERE nama_ruangan = ? AND lokasi = ?`;
    const params = [nama_ruangan, lokasi];
    if (excludeId) {
      sql += ` AND id != ?`;
      params.push(excludeId);
    }
    const [rows] = await db.query(sql, params);
    return rows.length > 0;
  }

  // Create ruangan
  static async create(nama_ruangan, kode_ruangan, lokasi) {
    const [result] = await db.query(
      `INSERT INTO ruangan (nama_ruangan, kode_ruangan, lokasi) VALUES (?, ?, ?)`,
      [nama_ruangan, kode_ruangan, lokasi]
    );
    return result.insertId;
  }

  // Update ruangan
  static async update(id, nama_ruangan, kode_ruangan, lokasi) {
    await db.query(
      `UPDATE ruangan SET nama_ruangan = ?, kode_ruangan = ?, lokasi = ? WHERE id = ?`,
      [nama_ruangan, kode_ruangan, lokasi, id]
    );
  }

  // Delete ruangan
  static async delete(id) {
    await db.query(`DELETE FROM ruangan WHERE id = ?`, [id]);
  }
}

module.exports = Ruangan;
