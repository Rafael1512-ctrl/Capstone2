const db = require('../config/db');

class DetailDraft {
  // Get items by draft ID
  static async getByDraftId(draftId) {
    const [rows] = await db.query(
      `SELECT * FROM detail_draft WHERE draft_id = ?`,
      [draftId]
    );
    return rows;
  }

  // Get pending items for inventaris
  static async getPendingInventaris() {
    const [rows] = await db.query(
      `SELECT dd.*, dp.tahun, u.nama AS pengaju
       FROM detail_draft dd
       JOIN draft_pengadaan dp ON dd.draft_id = dp.id
       JOIN users u ON dp.user_id = u.id
       LEFT JOIN inventaris iv ON iv.detail_draft_id = dd.id
       WHERE dp.status = 'finalized' 
         AND dd.status_item = 'approved' 
         AND dd.tipe_barang = 'inventaris'
         AND iv.id IS NULL`
    );
    return rows;
  }

  // Create item
  static async create(draftId, namaBarang, tipeBarang, hargaSatuan, jumlah, rasionalisasi = '', linkPembelian = '') {
    const [result] = await db.query(
      `INSERT INTO detail_draft (draft_id, nama_barang, tipe_barang, harga_satuan, jumlah, rasionalisasi, link_pembelian, status_item)
       VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')`,
      [draftId, namaBarang, tipeBarang, hargaSatuan, jumlah, rasionalisasi, linkPembelian]
    );
    return result.insertId;
  }

  // Delete item
  static async delete(id) {
    await db.query(`DELETE FROM detail_draft WHERE id = ?`, [id]);
  }

  // Approve all items in draft
  static async approveAllByDraftId(draftId) {
    await db.query(
      `UPDATE detail_draft SET status_item = 'approved' WHERE draft_id = ?`,
      [draftId]
    );
  }

  // Reject all items in draft
  static async rejectAllByDraftId(draftId) {
    await db.query(
      `UPDATE detail_draft SET status_item = 'rejected' WHERE draft_id = ?`,
      [draftId]
    );
  }

  // Get expenses (approved items)
  static async getTotalExpenses() {
    const [[{ totalExpenses }]] = await db.query(
      `SELECT COALESCE(SUM(jumlah * harga_satuan),0) AS totalExpenses 
       FROM detail_draft WHERE status_item='approved'`
    );
    return totalExpenses;
  }
}

module.exports = DetailDraft;
