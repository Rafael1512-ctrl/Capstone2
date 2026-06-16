const db = require('../config/db');

class Inventaris {
  // Get all received inventory
  static async getAllReceived() {
    const [rows] = await db.query(
      `SELECT iv.*, COALESCE(dd.harga_satuan, 0) as harga_satuan, r.nama_ruangan
       FROM inventaris iv
       LEFT JOIN detail_draft dd ON iv.detail_draft_id = dd.id
       LEFT JOIN ruangan r ON iv.ruangan_id = r.id
       WHERE iv.kondisi != 'dihapus'
       ORDER BY iv.id DESC`
    );
    return rows;
  }

  // Get single inventory item by ID
  static async getById(id) {
    const [rows] = await db.query(
      `SELECT iv.*, COALESCE(dd.harga_satuan, 0) as harga_satuan, r.nama_ruangan
       FROM inventaris iv
       LEFT JOIN detail_draft dd ON iv.detail_draft_id = dd.id
       LEFT JOIN ruangan r ON iv.ruangan_id = r.id
       WHERE iv.id = ? AND iv.kondisi != 'dihapus'`,
      [id]
    );
    return rows[0] || null;
  }

  // Get inventaris for maintenance
  static async getForMaintenance() {
    const [rows] = await db.query(
      `SELECT iv.*, dd.nama_barang 
       FROM inventaris iv
       JOIN detail_draft dd ON iv.detail_draft_id = dd.id
       WHERE iv.kondisi != 'dihapus'`
    );
    return rows;
  }

  // Receive inventaris item (with automatic soft delete of replaced item in transaction)
  static async receive(ruanganId, detailDraftId, nomorLabel, kondisi, tanggalTerima, qrCode) {
    const connection = await db.getConnection();
    await connection.beginTransaction();
    try {
      // 1. Insert new inventaris item
      const [result] = await connection.query(
        `INSERT INTO inventaris (ruangan_id, detail_draft_id, nama_barang, kategori, jenis, nomor_label, kondisi, tanggal_terima, barcode_qr)
         SELECT ?, ?, nama_barang, kategori, jenis, ?, ?, ?, ? FROM detail_draft WHERE id = ?`,
        [ruanganId, detailDraftId, nomorLabel, kondisi || 'baik', tanggalTerima || new Date(), qrCode, detailDraftId]
      );
      
      // 2. Check if this detail_draft item has an asset to replace
      const [ddRows] = await connection.query(
        `SELECT inventaris_digantikan_id FROM detail_draft WHERE id = ?`,
        [detailDraftId]
      );
      if (ddRows.length > 0 && ddRows[0].inventaris_digantikan_id) {
        // Change condition of the replaced item to 'dihapus'
        await connection.query(
          `UPDATE inventaris SET kondisi = 'dihapus' WHERE id = ?`,
          [ddRows[0].inventaris_digantikan_id]
        );
      }
      
      await connection.commit();
      connection.release();
      return result.insertId;
    } catch (err) {
      await connection.rollback();
      connection.release();
      throw err;
    }
  }

  // Update kondisi
  static async updateKondisi(id, kondisi) {
    await db.query(
      `UPDATE inventaris SET kondisi = ? WHERE id = ?`,
      [kondisi, id]
    );
  }

  static async attachUniversityQr(id, qrUnivPath, kodeInventarisUniv, tanggalDaftarUniv) {
    if (qrUnivPath) {
      await db.query(
        `UPDATE inventaris SET qr_univ_path = ?, kode_inventaris_univ = ?, tanggal_daftar_univ = ? WHERE id = ?`,
        [qrUnivPath, kodeInventarisUniv, tanggalDaftarUniv, id]
      );
    } else {
      await db.query(
        `UPDATE inventaris SET kode_inventaris_univ = ?, tanggal_daftar_univ = ? WHERE id = ?`,
        [kodeInventarisUniv, tanggalDaftarUniv, id]
      );
    }
  }

  // Soft delete inventaris
  static async softDelete(id) {
    await db.query(
      `UPDATE inventaris SET kondisi = 'dihapus' WHERE id = ?`,
      [id]
    );
  }

  // Check if label exists
  static async checkLabelExists(nomorLabel) {
    const [rows] = await db.query(
      `SELECT COUNT(*) as count FROM inventaris WHERE nomor_label = ? AND kondisi != 'dihapus'`,
      [nomorLabel]
    );
    return rows[0].count > 0;
  }

  // Get active inventory counts by condition
  static async getStats() {
    const [rows] = await db.query(
      `SELECT kondisi, COUNT(*) as count 
       FROM inventaris 
       WHERE kondisi != 'dihapus' 
       GROUP BY kondisi`
    );
    return rows;
  }
}

module.exports = Inventaris;
