const db = require("../config/db");

class DetailDraft {
  // Get items by draft ID
  static async getByDraftId(draftId) {
    const [rows] = await db.query(
      `SELECT dd.*, i.nomor_label AS label_digantikan, i.nama_barang AS nama_digantikan 
       FROM detail_draft dd
       LEFT JOIN inventaris i ON dd.inventaris_digantikan_id = i.id
       WHERE dd.draft_id = ?`,
      [draftId]
    );
    return rows;
  }

  // Get pending items for inventaris
  static async getPendingInventaris(draftId = null) {
    let sql = `SELECT dd.*, dp.tahun, u.nama AS pengaju,
              COALESCE(iv_count.cnt, 0) AS received_count
       FROM detail_draft dd
       JOIN draft_pengadaan dp ON dd.draft_id = dp.id
       JOIN users u ON dp.user_id = u.id
       LEFT JOIN (
         SELECT detail_draft_id, COUNT(*) AS cnt 
         FROM inventaris 
         GROUP BY detail_draft_id
       ) iv_count ON iv_count.detail_draft_id = dd.id
       WHERE dp.status = 'finalized' 
         AND dd.status_item = 'approved' 
         AND dd.tipe_barang = 'inventaris'
         AND COALESCE(iv_count.cnt, 0) < dd.jumlah`;

    const params = [];
    if (draftId) {
      sql += ` AND dd.draft_id = ?`;
      params.push(draftId);
    }

    const [rows] = await db.query(sql, params);
    return rows;
  }

  // Create item
  static async create(
    draftId,
    namaBarang,
    kategori,
    jenis,
    tipeBarang,
    hargaSatuan,
    jumlah,
    rasionalisasi = "",
    linkPembelian = "",
    inventarisDigantikanId = null
  ) {
    const [result] = await db.query(
      `INSERT INTO detail_draft (draft_id, nama_barang, kategori, jenis, tipe_barang, harga_satuan, jumlah, rasionalisasi, link_pembelian, inventaris_digantikan_id, status_item)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')`,
      [
        draftId,
        namaBarang,
        kategori || null,
        jenis || null,
        tipeBarang,
        hargaSatuan,
        jumlah,
        rasionalisasi,
        linkPembelian,
        inventarisDigantikanId || null
      ],
    );
    return result.insertId;
  }

  // Update item
  static async update(
    id,
    namaBarang,
    kategori,
    jenis,
    tipeBarang,
    hargaSatuan,
    jumlah,
    rasionalisasi = "",
    linkPembelian = "",
    inventarisDigantikanId = null
  ) {
    await db.query(
      `UPDATE detail_draft 
       SET nama_barang = ?, kategori = ?, jenis = ?, tipe_barang = ?, harga_satuan = ?, jumlah = ?, rasionalisasi = ?, link_pembelian = ?, inventaris_digantikan_id = ?
       WHERE id = ?`,
      [
        namaBarang,
        kategori || null,
        jenis || null,
        tipeBarang,
        hargaSatuan,
        jumlah,
        rasionalisasi,
        linkPembelian,
        inventarisDigantikanId || null,
        id
      ]
    );
  }

  // Delete item
  static async delete(id) {
    await db.query(`DELETE FROM detail_draft WHERE id = ?`, [id]);
  }

  // Approve all items in draft
  static async approveAllByDraftId(draftId) {
    await db.query(
      `UPDATE detail_draft SET status_item = 'approved' WHERE draft_id = ?`,
      [draftId],
    );
  }

  // Reject all items in draft
  static async rejectAllByDraftId(draftId) {
    await db.query(
      `UPDATE detail_draft SET status_item = 'rejected' WHERE draft_id = ?`,
      [draftId],
    );
  }

  // Update status for a specific item with catatan
  static async updateItemStatus(itemId, status, catatan = "") {
    await db.query(
      `UPDATE detail_draft SET status_item = ?, catatan = ? WHERE id = ?`,
      [status, catatan || null, itemId],
    );
  }

  // Get expenses (approved items)
  static async getTotalExpenses() {
    const [[{ totalExpenses }]] = await db.query(
      `SELECT COALESCE(SUM(jumlah * harga_satuan),0) AS totalExpenses 
       FROM detail_draft WHERE status_item='approved'`,
    );
    return totalExpenses;
  }
}

module.exports = DetailDraft;
