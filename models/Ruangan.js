const db = require('../config/db');

class Ruangan {
  // Get all ruangan
  static async getAll() {
    const [rows] = await db.query(`SELECT * FROM ruangan ORDER BY id DESC`);
    return rows;
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
