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
  static async create(namaBhp, ruanganId, stok, satuan, kondisi, stokMinimum) {
    const [result] = await db.query(
      `INSERT INTO bhp (nama_bhp, ruangan_id, stok, satuan, kondisi, stok_minimum) VALUES (?, ?, ?, ?, ?, ?)`,
      [namaBhp, ruanganId, stok || 0, satuan || 'pcs', kondisi || 'baik', stokMinimum || 0]
    );
    return result.insertId;
  }

  // Update stock
  static async updateStock(id, stok, kondisi, stokMinimum) {
    await db.query(
      `UPDATE bhp SET stok = ?, kondisi = ?, stok_minimum = ? WHERE id = ?`,
      [stok, kondisi || 'baik', stokMinimum || 0, id]
    );
  }

  // Reduce stock
  static async reduceStock(id, qty) {
    await db.query(
      `UPDATE bhp SET stok = GREATEST(0, stok - ?) WHERE id = ?`,
      [qty, id]
    );
  }

  // Get BHP mutasi history based on maintenance logs
  static async getMutasiHistory() {
    const [rows] = await db.query(
      `SELECT m.id, m.tanggal_maintenance AS tanggal, b.nama_bhp, m.jumlah_bhp_digunakan AS jumlah,
              b.satuan, iv.nama_barang AS nama_aset, iv.nomor_label, u.nama AS petugas, m.deskripsi
       FROM maintenance_log m
       JOIN bhp b ON m.bhp_digunakan_id = b.id
       JOIN inventaris iv ON m.inventaris_id = iv.id
       JOIN users u ON m.staf_lab_id = u.id
       WHERE m.bhp_digunakan_id IS NOT NULL AND m.jumlah_bhp_digunakan > 0
       ORDER BY m.tanggal_maintenance DESC, m.id DESC`
    );
    return rows;
  }
}

module.exports = BHP;
