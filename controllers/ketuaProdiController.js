const DraftPengadaan = require('../models/DraftPengadaan');
const DetailDraft = require('../models/DetailDraft');

class KetuaProdiController {
  // GET /api/ketua-prodi/review
  static async showReview(req, res) {
    try {
      const drafts = await DraftPengadaan.getDraftsForReview(req.user.id);
      res.json({
        title: 'Review Draf Pengadaan',
        activePath: '/ketua-prodi/review',
        drafts,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/ketua-prodi/review/:id
  static async showReviewDetail(req, res) {
    try {
      const draft = await DraftPengadaan.getById(req.params.id);
      const items = await DetailDraft.getByDraftId(req.params.id);
      const activePath = ['finalized', 'rejected'].includes(draft.status) ? '/ketua-prodi/history' : '/ketua-prodi/review';

      res.json({
        title: `Detail Review Draf #${draft.id}`,
        activePath,
        draft,
        items
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/ketua-prodi/review/:id/process
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
      res.json({ success: true, message: `Draft status updated to ${action}` });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/ketua-prodi/history
  static async showHistory(req, res) {
    try {
      const drafts = await DraftPengadaan.getHistoryForKetuaProdi(req.user.id);
      res.json({
        title: 'Riwayat Draf Pengadaan',
        activePath: '/ketua-prodi/history',
        drafts,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }
}

module.exports = KetuaProdiController;
