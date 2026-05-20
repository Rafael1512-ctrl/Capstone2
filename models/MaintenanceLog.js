const db = require('../config/db');

class MaintenanceLog {
  // Get all logs
  static async getAll() {
    const [rows] = await db.query(
      `SELECT m.*, i.nomor_label, u.nama AS petugas, b.nama_bhp
       FROM maintenance_log m
       JOIN inventaris i ON m.inventaris_id = i.id
       JOIN users u ON m.user_id = u.id
       LEFT JOIN bhp b ON m.bhp_id_used = b.id
       ORDER BY m.id DESC`
    );
    return rows;
  }

  // Create maintenance log with transaction
  static async create(inventarisId, userId, bhpIdUsed, qtyBhpUsed, deskripsi, statusAkhir) {
    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
      // 1. Simpan log maintenance
      const [result] = await connection.query(
        `INSERT INTO maintenance_log (inventaris_id, user_id, bhp_id_used, qty_bhp_used, deskripsi, tanggal_maintenance)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [inventarisId, userId, bhpIdUsed || null, bhpIdUsed ? (qtyBhpUsed || 0) : 0, deskripsi || '', new Date()]
      );

      // 2. Update status kondisi barang
      await connection.query(
        `UPDATE inventaris SET kondisi = ? WHERE id = ?`,
        [statusAkhir || 'baik', inventarisId]
      );

      // 3. Potong stok BHP jika ada
      if (bhpIdUsed && qtyBhpUsed > 0) {
        await connection.query(
          `UPDATE bhp SET stok = GREATEST(0, stok - ?) WHERE id = ?`,
          [qtyBhpUsed, bhpIdUsed]
        );
      }

      await connection.commit();
      connection.release();
      return result.insertId;
    } catch (transactionErr) {
      await connection.rollback();
      connection.release();
      throw transactionErr;
    }
  }
}

module.exports = MaintenanceLog;
