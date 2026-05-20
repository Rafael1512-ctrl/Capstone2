const DraftPengadaan = require('../models/DraftPengadaan');
const DetailDraft = require('../models/DetailDraft');

class KetuaProdiController {
  // GET /ketua-prodi/review
  static async showReview(req, res) {
    try {
      const drafts = await DraftPengadaan.getDraftsForReview(req.session.user.id);
      res.render('ketua_prodi/review', {
        title: 'Review Draf Pengadaan',
        activePath: '/ketua-prodi/review',
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

  // GET /ketua-prodi/review/:id
  static async showReviewDetail(req, res) {
    try {
      const draft = await DraftPengadaan.getById(req.params.id);
      const items = await DetailDraft.getByDraftId(req.params.id);
      const activePath = ['finalized', 'rejected'].includes(draft.status) ? '/ketua-prodi/history' : '/ketua-prodi/review';

      res.render('ketua_prodi/detail', {
        title: `Detail Review Draf #${draft.id}`,
        activePath,
        draft,
        items
      });
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // POST /ketua-prodi/review/:id/process
  static async processDraft(req, res) {
    try {
      const { action, alasan_penolakan } = req.body;
      if (action === 'approve') {
        await DraftPengadaan.approve(req.params.id, alasan_penolakan);
        await DetailDraft.approveAllByDraftId(req.params.id);
      } else if (action === 'reject') {
        await DraftPengadaan.reject(req.params.id, alasan_penolakan);
        await DetailDraft.rejectAllByDraftId(req.params.id);
      }
      res.redirect('/ketua-prodi/history');
    } catch (err) {
      console.error(err);
      res.status(500).send('Database error: ' + err.message);
    }
  }

  // GET /ketua-prodi/history
  static async showHistory(req, res) {
    try {
      const drafts = await DraftPengadaan.getHistoryForKetuaProdi(req.session.user.id);
      res.render('ketua_prodi/history', {
        title: 'Riwayat Draf Pengadaan',
        activePath: '/ketua-prodi/history',
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
}

module.exports = KetuaProdiController;
