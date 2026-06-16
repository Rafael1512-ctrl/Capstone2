const fs = require('fs');
const path = require('path');
const DraftPengadaan = require("../models/DraftPengadaan");
const DetailDraft = require("../models/DetailDraft");
const Inventaris = require("../models/Inventaris");
const Ruangan = require("../models/Ruangan");

class StafAdminController {
  // GET /api/staf-admin/drafts
  static async showDrafts(req, res) {
    try {
      const drafts = await DraftPengadaan.getSubmittedDrafts();
      res.json({
        title: "Draf Pengadaan Disetujui",
        activePath: "/staf-admin/drafts",
        drafts,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // GET /api/inventaris
  static async showInventaris(req, res) {
    try {
      const receivedItems = await Inventaris.getAllReceived();

      let pendingItems = [];
      let ruangan = [];
      let drafts = [];

      if (req.user && req.user.role === "staf_admin") {
        const rawDraftId = req.query.draft_id;
        const draftId =
          rawDraftId && !isNaN(rawDraftId) ? parseInt(rawDraftId) : null;
        pendingItems = await DetailDraft.getPendingInventaris(draftId);
        ruangan = await Ruangan.getAll();
        drafts = await DraftPengadaan.getSubmittedDrafts();
      }

      res.json({
        title: "Daftar Inventaris",
        activePath: "/inventaris",
        pendingItems,
        receivedItems,
        ruangan,
        drafts,
        hasPendingItems: pendingItems.length > 0,
        pendingCount: pendingItems.length,
        hasReceivedItems: receivedItems.length > 0,
        hasRuangan: ruangan.length > 0,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/staf-admin/inventaris/receive/:itemId
  static async receiveItem(req, res) {
    try {
      const { nomor_label, ruangan_id, tanggal_terima } = req.body;
      const kondisi = req.body.kondisi || "baik";

      // Check if label already exists to avoid InnoDB auto-increment jump on duplicate key error
      const labelExists = await Inventaris.checkLabelExists(nomor_label);
      if (labelExists) {
        return res.json({
          error: `Label "${nomor_label}" sudah terdaftar. Silakan gunakan nomor label lain.`,
        });
      }

      const qrMock = `QR-${nomor_label}.png`;

      await Inventaris.receive(
        ruangan_id,
        req.params.itemId,
        nomor_label,
        kondisi,
        tanggal_terima,
        qrMock,
      );
      res.json({
        success: true,
        message: "Item received and labeled successfully",
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/staf-admin/inventaris/upload-qr-univ/:id
  static async uploadUniversityQr(req, res) {
    try {
      const { qr_univ_file_name, qr_univ_file_data, kode_inventaris_univ, tanggal_daftar_univ, qr_univ_path } = req.body;

      let fileUrl = qr_univ_path || null;
      if (!fileUrl && qr_univ_file_name && qr_univ_file_data) {
        const uploadDir = path.join(__dirname, '..', 'public', 'uploads', 'inventaris');
        fs.mkdirSync(uploadDir, { recursive: true });

        const extension = path.extname(qr_univ_file_name).toLowerCase() || '.png';
        const safeName = `qr_univ_inventaris_${req.params.id}_${Date.now()}${extension}`;
        const filePath = path.join(uploadDir, safeName);
        const fileBuffer = Buffer.from(qr_univ_file_data, 'base64');

        fs.writeFileSync(filePath, fileBuffer);
        fileUrl = `${req.protocol}://${req.get('host')}/uploads/inventaris/${safeName}`;
      }

      await Inventaris.attachUniversityQr(
        req.params.id,
        fileUrl,
        kode_inventaris_univ || fileUrl,
        tanggal_daftar_univ || null
      );

      res.json({ success: true, message: 'QR Universitas berhasil diunggah.', qr_univ_path: fileUrl });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/staf-admin/inventaris/delete/:id
  static async deleteInventaris(req, res) {
    try {
      await Inventaris.softDelete(req.params.id);
      res.json({
        success: true,
        message: "Inventaris soft-deleted successfully",
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }
}

module.exports = StafAdminController;
