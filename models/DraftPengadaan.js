const db = require("../config/db");

class DraftPengadaan {
  // Get draft by ID with user
  static async getById(id) {
    const [[draft]] = await db.query(
      `SELECT d.*, u.nama AS pengaju FROM draft_pengadaan d 
       JOIN users u ON d.user_id = u.id WHERE d.id = ?`,
      [id],
    );
    return draft;
  }

  // Get active draft for user
  static async getActiveDraft(userId) {
    const [drafts] = await db.query(
      `SELECT * FROM draft_pengadaan WHERE user_id = ? AND status = 'draft'`,
      [userId],
    );
    return drafts.length > 0 ? drafts[0] : null;
  }

  // Get all submitted/reviewed drafts for ketua prodi
  static async getDraftsForReview(ketuaProdiId) {
    const [drafts] = await db.query(
      `SELECT d.*, u.nama AS pengaju,
              (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id) AS total_items,
              (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id AND status_item='pending') AS pending_items
       FROM draft_pengadaan d
       JOIN users u ON d.user_id = u.id
       WHERE d.status IN ('submitted', 'reviewed') AND d.ketua_prodi_id = ?
       ORDER BY d.id DESC`,
      [ketuaProdiId],
    );
    return drafts;
  }

  // Get completed/rejected drafts for ketua prodi
  static async getHistoryForKetuaProdi(ketuaProdiId) {
    const [drafts] = await db.query(
      `SELECT d.*, u.nama AS pengaju,
              (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id) AS total_items
       FROM draft_pengadaan d
       JOIN users u ON d.user_id = u.id
       WHERE d.status IN ('finalized', 'rejected') AND d.ketua_prodi_id = ?
       ORDER BY d.id DESC`,
      [ketuaProdiId],
    );
    return drafts;
  }

  // Get submitted drafts for staf admin
  static async getSubmittedDrafts() {
    const [drafts] = await db.query(
      `SELECT d.*, u.nama AS pengaju,
              (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id AND status_item='approved') AS approved_items
       FROM draft_pengadaan d
       JOIN users u ON d.user_id = u.id
       WHERE d.status = 'finalized'
       ORDER BY d.id DESC`,
    );
    return drafts;
  }

  // Get history for kepala lab
  static async getHistoryForKepalaLab(userId) {
    const [drafts] = await db.query(
      `SELECT d.*, (SELECT COUNT(*) FROM detail_draft WHERE draft_id = d.id) AS item_count
       FROM draft_pengadaan d
       WHERE d.user_id = ? AND d.status != 'draft'
       ORDER BY d.id DESC`,
      [userId],
    );
    return drafts;
  }

  // Create draft
  static async create(userId, ketuaProdiId, tahun) {
    const [result] = await db.query(
      `INSERT INTO draft_pengadaan (user_id, ketua_prodi_id, tahun, status) VALUES (?, ?, ?, 'draft')`,
      [userId, ketuaProdiId || null, tahun || new Date().getFullYear()],
    );
    return result.insertId;
  }

  // Update draft
  static async update(id, tahun, ketuaProdiId, userId) {
    await db.query(
      `UPDATE draft_pengadaan SET tahun = ?, ketua_prodi_id = ? WHERE id = ? AND user_id = ? AND status = 'draft'`,
      [tahun, ketuaProdiId || null, id, userId],
    );
  }

  // Delete draft
  static async delete(id, userId) {
    await db.query(
      `DELETE FROM draft_pengadaan WHERE id = ? AND user_id = ? AND status = 'draft'`,
      [id, userId],
    );
  }

  // Submit draft
  static async submit(id) {
    await db.query(
      `UPDATE draft_pengadaan SET status = 'submitted' WHERE id = ?`,
      [id],
    );
  }

  // Approve draft (finalized)
  static async approve(id, alasanPenolakan = "") {
    await db.query(
      `UPDATE draft_pengadaan SET status = 'finalized', alasan_penolakan = ? WHERE id = ?`,
      [alasanPenolakan, id],
    );
  }

  // Reject draft
  static async reject(id, alasanPenolakan = "") {
    await db.query(
      `UPDATE draft_pengadaan SET status = 'rejected', alasan_penolakan = ? WHERE id = ?`,
      [alasanPenolakan, id],
    );
  }

  // Update draft status with optional reason
  static async updateStatus(id, status, alasan = "") {
    await db.query(
      `UPDATE draft_pengadaan SET status = ?, alasan_penolakan = ? WHERE id = ?`,
      [status, alasan || null, id],
    );
  }
}

module.exports = DraftPengadaan;
