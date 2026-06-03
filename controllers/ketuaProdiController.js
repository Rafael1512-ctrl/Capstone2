const DraftPengadaan = require("../models/DraftPengadaan");
const DetailDraft = require("../models/DetailDraft");

class KetuaProdiController {
  // GET /api/ketua-prodi/review
  static async showReview(req, res) {
    try {
      const drafts = await DraftPengadaan.getDraftsForReview(req.user.id);
      res.json({
        title: "Review Draf Pengadaan",
        activePath: "/ketua-prodi/review",
        drafts,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // GET /api/ketua-prodi/review/:id
  static async showReviewDetail(req, res) {
    try {
      const draft = await DraftPengadaan.getById(req.params.id);
      const items = await DetailDraft.getByDraftId(req.params.id);
      const activePath = ["finalized", "rejected"].includes(draft.status)
        ? "/ketua-prodi/history"
        : "/ketua-prodi/review";

      res.json({
        title: `Detail Review Draf #${draft.id}`,
        activePath,
        draft,
        items,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/ketua-prodi/review/:id/process
  static async processDraft(req, res) {
    try {
      const draftId = req.params.id;
      const decisions = req.body.decision || {};
      const catatan = req.body.catatan_item || {};

      // Update each item with its decision and catatan
      for (const [itemId, status] of Object.entries(decisions)) {
        await DetailDraft.updateItemStatus(
          itemId,
          status,
          catatan[itemId] || "",
        );
      }

      // Get all items to determine draft status
      const allItems = await DetailDraft.getByDraftId(draftId);
      const hasPending = allItems.some((i) => i.status_item === "pending");
      const hasApproved = allItems.some((i) => i.status_item === "approved");
      const hasRejected = allItems.some((i) => i.status_item === "rejected");

      // Determine new draft status
      let newStatus = "reviewed";
      if (!hasPending) {
        if (hasApproved) {
          newStatus = "finalized";
        } else {
          newStatus = "rejected";
        }
      }

      await DraftPengadaan.updateStatus(
        draftId,
        newStatus,
        req.body.alasan_penolakan || "",
      );
      res.json({
        success: true,
        message: "Keputusan berhasil disimpan",
        status: newStatus,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // GET /api/ketua-prodi/history
  static async showHistory(req, res) {
    try {
      const drafts = await DraftPengadaan.getHistoryForKetuaProdi(req.user.id);
      res.json({
        title: "Riwayat Draf Pengadaan",
        activePath: "/ketua-prodi/history",
        drafts,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }
}

module.exports = KetuaProdiController;
