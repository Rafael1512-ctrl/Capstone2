const DraftPengadaan = require('../models/DraftPengadaan');
const DetailDraft = require('../models/DetailDraft');
const User = require('../models/User');

class KepalaLabController {
  // GET /kepala-lab/pengadaan
  static async showPengadaan(req, res) {
    try {
      const activeDraft = await DraftPengadaan.getActiveDraft(req.session.user.id);
      let items = [];
      if (activeDraft) {
        items = await DetailDraft.getByDraftId(activeDraft.id);
      }
      const kaprodiList = await User.getKetuaProdiList();

      res.render('kepala_lab/pengadaan', {
        title: 'Draf Pengadaan Baru',
        activePath: '/kepala-lab/pengadaan',
        activeDraft,
        items,
        kaprodiList,
        // Helper flags untuk template
        hasActiveDraft: !!activeDraft,
        hasItems: items.length > 0,
        itemCount: items.length,
        hasKaprodiList: kaprodiList.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /kepala-lab/pengadaan/create-draft
  static async createDraft(req, res) {
    try {
      const { tahun, ketua_prodi_id } = req.body;
      await DraftPengadaan.create(req.session.user.id, ketua_prodi_id, tahun);
      res.redirect('/kepala-lab/pengadaan');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /kepala-lab/pengadaan/update-draft/:id
  static async updateDraft(req, res) {
    try {
      const { tahun, ketua_prodi_id } = req.body;
      await DraftPengadaan.update(req.params.id, tahun, ketua_prodi_id, req.session.user.id);
      res.redirect('/kepala-lab/pengadaan');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /kepala-lab/pengadaan/delete-draft/:id
  static async deleteDraft(req, res) {
    try {
      await DraftPengadaan.delete(req.params.id, req.session.user.id);
      res.redirect('/kepala-lab/pengadaan');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /kepala-lab/pengadaan/add-item
  static async addItem(req, res) {
    try {
      const { draft_id, nama_barang, tipe_barang, harga_satuan, jumlah, rasionalisasi, link_pembelian } = req.body;
      await DetailDraft.create(draft_id, nama_barang, tipe_barang, harga_satuan, jumlah, rasionalisasi, link_pembelian);
      res.redirect('/kepala-lab/pengadaan');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /kepala-lab/pengadaan/delete-item/:id
  static async deleteItem(req, res) {
    try {
      await DetailDraft.delete(req.params.id);
      res.redirect('/kepala-lab/pengadaan');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /kepala-lab/pengadaan/submit/:id
  static async submitDraft(req, res) {
    try {
      await DraftPengadaan.submit(req.params.id);
      res.redirect('/kepala-lab/history');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // GET /kepala-lab/history
  static async showHistory(req, res) {
    try {
      const drafts = await DraftPengadaan.getHistoryForKepalaLab(req.session.user.id);
      const activeDraft = await DraftPengadaan.getActiveDraft(req.session.user.id);
      let items = [];
      if (activeDraft) {
        items = await DetailDraft.getByDraftId(activeDraft.id);
      }

      res.render('kepala_lab/history', {
        title: 'Riwayat Pengadaan',
        activePath: '/kepala-lab/history',
        drafts,
        activeDraft,
        items,
        // Helper flags untuk template
        hasActiveDraft: !!activeDraft,
        hasItems: items.length > 0,
        itemCount: items.length,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }
}

module.exports = KepalaLabController;
