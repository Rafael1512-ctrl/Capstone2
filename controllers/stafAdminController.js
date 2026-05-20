const DraftPengadaan = require('../models/DraftPengadaan');
const DetailDraft = require('../models/DetailDraft');
const Inventaris = require('../models/Inventaris');
const Ruangan = require('../models/Ruangan');

class StafAdminController {
  // GET /staf-admin/drafts
  static async showDrafts(req, res) {
    try {
      const drafts = await DraftPengadaan.getSubmittedDrafts();
      res.render('staf_admin/drafts', {
        title: 'Draf Pengadaan Disetujui',
        activePath: '/staf-admin/drafts',
        drafts,
        // Helper flags
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // GET /staf-admin/inventaris
  static async showInventaris(req, res) {
    try {
      const pendingItems = await DetailDraft.getPendingInventaris();
      const receivedItems = await Inventaris.getAllReceived();
      const ruangan = await Ruangan.getAll();

      res.render('staf_admin/inventaris', {
        title: 'Update & Labeling Inventaris',
        activePath: '/staf-admin/inventaris',
        pendingItems,
        receivedItems,
        ruangan,
        // Helper flags
        hasPendingItems: pendingItems.length > 0,
        pendingCount: pendingItems.length,
        hasReceivedItems: receivedItems.length > 0,
        hasRuangan: ruangan.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /staf-admin/inventaris/receive/:itemId
  static async receiveItem(req, res) {
    try {
      const { nomor_label, ruangan_id, tanggal_terima, kondisi } = req.body;
      const qrMock = `QR-${nomor_label}.png`;

      await Inventaris.receive(ruangan_id, req.params.itemId, nomor_label, kondisi, tanggal_terima, qrMock);
      res.redirect('/staf-admin/inventaris');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }
}

module.exports = StafAdminController;
