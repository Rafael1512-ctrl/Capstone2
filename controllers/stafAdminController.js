const DraftPengadaan = require('../models/DraftPengadaan');
const DetailDraft = require('../models/DetailDraft');
const Inventaris = require('../models/Inventaris');
const Ruangan = require('../models/Ruangan');

class StafAdminController {
  // GET /api/staf-admin/drafts
  static async showDrafts(req, res) {
    try {
      const drafts = await DraftPengadaan.getSubmittedDrafts();
      res.json({
        title: 'Draf Pengadaan Disetujui',
        activePath: '/staf-admin/drafts',
        drafts,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/staf-admin/inventaris
  static async showInventaris(req, res) {
    try {
      const pendingItems = await DetailDraft.getPendingInventaris();
      const receivedItems = await Inventaris.getAllReceived();
      const ruangan = await Ruangan.getAll();

      res.json({
        title: 'Update & Labeling Inventaris',
        activePath: '/staf-admin/inventaris',
        pendingItems,
        receivedItems,
        ruangan,
        hasPendingItems: pendingItems.length > 0,
        pendingCount: pendingItems.length,
        hasReceivedItems: receivedItems.length > 0,
        hasRuangan: ruangan.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/staf-admin/inventaris/receive/:itemId
  static async receiveItem(req, res) {
    try {
      const { nomor_label, ruangan_id, tanggal_terima, kondisi } = req.body;

      // Check if label already exists to avoid InnoDB auto-increment jump on duplicate key error
      const labelExists = await Inventaris.checkLabelExists(nomor_label);
      if (labelExists) {
        return res.json({ error: `Label "${nomor_label}" sudah terdaftar. Silakan gunakan nomor label lain.` });
      }

      const qrMock = `QR-${nomor_label}.png`;

      await Inventaris.receive(ruangan_id, req.params.itemId, nomor_label, kondisi, tanggal_terima, qrMock);
      res.json({ success: true, message: 'Item received and labeled successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }
}

module.exports = StafAdminController;
