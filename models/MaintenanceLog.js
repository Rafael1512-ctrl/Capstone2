const db = require('../config/db');

class MaintenanceLog {
  // Get all logs
  static async getAll() {
    const [rows] = await db.query(
      `SELECT m.*, m.jumlah_bhp_digunakan AS qty_bhp_used, i.nomor_label, u.nama AS petugas, b.nama_bhp
       FROM maintenance_log m
       JOIN inventaris i ON m.inventaris_id = i.id
       JOIN users u ON m.staf_lab_id = u.id
       LEFT JOIN bhp b ON m.bhp_digunakan_id = b.id
       ORDER BY m.id DESC`
    );
    return rows;
  }

  // Create maintenance log with transaction
  static async create(inventarisId, userId, bhpIdUsed, qtyBhpUsed, deskripsi, statusAkhir) {
    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
      // 1. Dapatkan kondisi sebelum dari inventaris
      // User request: "anggepla semua yang di mt kalo dimasukin ke log itu berarti dia rusak kondisi awalnya"
      const kondisiSebelum = 'rusak';

      // 2. Simpan log maintenance
      const [result] = await connection.query(
        `INSERT INTO maintenance_log (inventaris_id, staf_lab_id, tanggal_maintenance, deskripsi, kondisi_sebelum, kondisi_sesudah, bhp_digunakan_id, jumlah_bhp_digunakan)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          inventarisId,
          userId,
          new Date(),
          deskripsi || '',
          kondisiSebelum,
          statusAkhir || 'baik',
          bhpIdUsed || null,
          bhpIdUsed ? (qtyBhpUsed || 0) : null
        ]
      );

      // 3. Update status kondisi barang di inventaris
      await connection.query(
        `UPDATE inventaris SET kondisi = ? WHERE id = ?`,
        [statusAkhir || 'baik', inventarisId]
      );

      // 4. Potong stok BHP jika ada
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
