const db = require('../config/db');

class Inventaris {
  // Get all received inventory
  static async getAllReceived() {
    const [rows] = await db.query(
      `SELECT iv.*, dd.nama_barang, dd.harga_satuan, r.nama_ruangan
       FROM inventaris iv
       JOIN detail_draft dd ON iv.detail_draft_id = dd.id
       LEFT JOIN ruangan r ON iv.ruangan_id = r.id
       ORDER BY iv.id DESC`
    );
    return rows;
  }

  // Get inventaris for maintenance
  static async getForMaintenance() {
    const [rows] = await db.query(
      `SELECT iv.*, dd.nama_barang 
       FROM inventaris iv
       JOIN detail_draft dd ON iv.detail_draft_id = dd.id`
    );
    return rows;
  }

  // Receive inventaris item
  static async receive(ruanganId, detailDraftId, nomorLabel, kondisi, tanggalTerima, qrCode) {
    const [result] = await db.query(
      `INSERT INTO inventaris (ruangan_id, detail_draft_id, nama_barang, nomor_label, kondisi, tanggal_terima, barcode_qr)
       SELECT ?, ?, nama_barang, ?, ?, ?, ? FROM detail_draft WHERE id = ?`,
      [ruanganId, detailDraftId, nomorLabel, kondisi || 'baik', tanggalTerima || new Date(), qrCode, detailDraftId]
    );
    return result.insertId;
  }

  // Update kondisi
  static async updateKondisi(id, kondisi) {
    await db.query(
      `UPDATE inventaris SET kondisi = ? WHERE id = ?`,
      [kondisi, id]
    );
  }

  // Check if label exists
  static async checkLabelExists(nomorLabel) {
    const [rows] = await db.query(
      `SELECT COUNT(*) as count FROM inventaris WHERE nomor_label = ?`,
      [nomorLabel]
    );
    return rows[0].count > 0;
  }
}

module.exports = Inventaris;
