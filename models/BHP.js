const db = require('../config/db');

class BHP {
  // Get all BHP
  static async getAll() {
    const [rows] = await db.query(
      `SELECT b.*, r.nama_ruangan 
       FROM bhp b
       LEFT JOIN ruangan r ON b.ruangan_id = r.id
       ORDER BY b.id DESC`
    );
    return rows;
  }

  // Get BHP with stock > 0
  static async getInStock() {
    const [rows] = await db.query(
      `SELECT id, nama_bhp, stok FROM bhp WHERE stok > 0`
    );
    return rows;
  }

  // Get BHP by ID
  static async getById(id) {
    const [rows] = await db.query(
      `SELECT * FROM bhp WHERE id = ?`,
      [id]
    );
    return rows.length > 0 ? rows[0] : null;
  }

  // Create BHP
  static async create(namaBhp, ruanganId, stok, satuan, kondisi) {
    const [result] = await db.query(
      `INSERT INTO bhp (nama_bhp, ruangan_id, stok, satuan, kondisi) VALUES (?, ?, ?, ?, ?)`,
      [namaBhp, ruanganId, stok || 0, satuan || 'pcs', kondisi || 'baik']
    );
    return result.insertId;
  }

  // Update stock
  static async updateStock(id, stok, kondisi) {
    await db.query(
      `UPDATE bhp SET stok = ?, kondisi = ? WHERE id = ?`,
      [stok, kondisi || 'baik', id]
    );
  }

  // Reduce stock
  static async reduceStock(id, qty) {
    await db.query(
      `UPDATE bhp SET stok = GREATEST(0, stok - ?) WHERE id = ?`,
      [qty, id]
    );
  }
}

module.exports = BHP;
