-- New tracking codes (e.g. CICS-3339-TRANS-2026-001, plus "-VOID-n" archive copies)
-- are longer than 20 characters; match documents.tracking_id (varchar 50).
ALTER TABLE `notifications` MODIFY `tracking_id` VARCHAR(50) NULL;
