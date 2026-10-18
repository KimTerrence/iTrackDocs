<div class="w-full max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-sm text-center">
        <span class="font-mono text-brand-600 dark:text-brand-400 font-bold px-3 py-1 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-sm"><?= htmlspecialchars($tracking_id) ?></span>
        <h1 class="text-xl font-extrabold text-slate-900 dark:text-white mt-4">Adding the tracking code to the uploaded document</h1>
        <p id="stampStatus" class="text-sm text-slate-500 dark:text-slate-400 mt-2">Please wait...</p>
        <div id="stampError" class="hidden mt-6 space-y-3">
            <p id="stampErrorText" class="text-sm font-semibold text-red-600"></p>
            <a href="<?= htmlspecialchars($nextUrl) ?>" class="inline-flex px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm rounded-xl">
                Continue without the code
            </a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
<script>
(function () {
    const TRACKING_ID = <?= json_encode($tracking_id) ?>;
    const FILE_URL = <?= json_encode($document['file_path']) ?>;
    const FILE_TYPE = <?= json_encode($fileType) ?>;
    const NEXT_URL = <?= json_encode($nextUrl) ?>;
    const statusEl = document.getElementById('stampStatus');

    // Same look as the letters: bold Times, top-right inside the margin, on every page.
    // A white box behind the text keeps it readable on top of scanned content.

    async function stampPdf(bytes) {
        const { PDFDocument, StandardFonts, rgb, degrees } = PDFLib;
        const pdf = await PDFDocument.load(bytes); // throws on encrypted PDFs
        const font = await pdf.embedFont(StandardFonts.TimesRomanBold);

        pdf.getPages().forEach(page => {
            const box = page.getCropBox();
            const rotation = ((page.getRotation().angle % 360) + 360) % 360;
            const sideways = rotation === 90 || rotation === 270;
            const vw = sideways ? box.height : box.width;   // page size as it is displayed
            const vh = sideways ? box.width : box.height;

            const size = Math.max(9, Math.min(14, vw / 61));  // 10pt on letter/legal paper
            const margin = Math.max(18, vw * 0.045);
            const pad = size * 0.3;
            const textWidth = font.widthOfTextAtSize(TRACKING_ID, size);
            const vx = vw - margin - textWidth;                // baseline start, in displayed coordinates
            const vy = vh - margin - size;

            // Displayed coordinates -> page coordinates for the page's /Rotate value
            const toPage = (x, y) => {
                if (rotation === 90) return { x: box.x + box.width - y, y: box.y + x };
                if (rotation === 180) return { x: box.x + box.width - x, y: box.y + box.height - y };
                if (rotation === 270) return { x: box.x + y, y: box.y + box.height - x };
                return { x: box.x + x, y: box.y + y };
            };
            const rotate = degrees(rotation);

            const r = toPage(vx - pad, vy - pad - size * 0.22);
            page.drawRectangle({ x: r.x, y: r.y, width: textWidth + pad * 2, height: size * 1.22 + pad * 2, color: rgb(1, 1, 1), rotate });
            const t = toPage(vx, vy);
            page.drawText(TRACKING_ID, { x: t.x, y: t.y, size, font, color: rgb(0, 0, 0), rotate });
        });
        return new Blob([await pdf.save()], { type: 'application/pdf' });
    }

    async function stampImage(blob) {
        const img = await new Promise((resolve, reject) => {
            const el = new Image();
            el.onload = () => resolve(el);
            el.onerror = () => reject(new Error('The image could not be read.'));
            el.src = URL.createObjectURL(blob);
        });
        const canvas = document.createElement('canvas');
        canvas.width = img.naturalWidth;
        canvas.height = img.naturalHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);

        const size = Math.max(14, canvas.width * 0.0163);   // 10pt on a 300 dpi letter scan
        const margin = canvas.width * 0.045;
        const pad = size * 0.3;
        ctx.font = 'bold ' + size + 'px "Times New Roman", Times, serif';
        const textWidth = ctx.measureText(TRACKING_ID).width;
        const x = canvas.width - margin - textWidth;
        const y = margin;
        ctx.fillStyle = '#fff';
        ctx.fillRect(x - pad, y - pad, textWidth + pad * 2, size * 1.2 + pad * 2);
        ctx.fillStyle = '#000';
        ctx.textBaseline = 'top';
        ctx.fillText(TRACKING_ID, x, y);

        const mime = FILE_TYPE === 'png' ? 'image/png' : 'image/jpeg';
        return new Promise((resolve, reject) => canvas.toBlob(b => b ? resolve(b) : reject(new Error('Could not create the image.')), mime, 0.92));
    }

    async function run() {
        try {
            const res = await fetch(FILE_URL, { cache: 'no-store' });
            if (!res.ok) throw new Error('The uploaded file could not be loaded (' + res.status + ').');
            const original = await res.blob();

            statusEl.textContent = 'Stamping ' + TRACKING_ID + ' on the document...';
            const stamped = FILE_TYPE === 'pdf' ? await stampPdf(await original.arrayBuffer()) : await stampImage(original);

            statusEl.textContent = 'Saving...';
            const form = new FormData();
            form.append('tracking_id', TRACKING_ID);
            form.append('stamped', stamped, TRACKING_ID + '.' + FILE_TYPE);
            const save = await fetch('index.php?action=save_stamped', { method: 'POST', body: form, credentials: 'same-origin' });
            const data = await save.json().catch(() => ({ error: 'Unexpected server response.' }));
            if (!save.ok || !data.ok) throw new Error(data.error || 'Saving failed.');

            statusEl.textContent = 'Done.';
            window.location.replace(NEXT_URL);
        } catch (err) {
            const encrypted = /encrypt/i.test(err.message);
            statusEl.textContent = 'The tracking code could not be added.';
            document.getElementById('stampErrorText').textContent = encrypted
                ? 'This PDF is password-protected or encrypted, so it cannot be changed. The original file was kept.'
                : err.message + ' The original file was kept.';
            document.getElementById('stampError').classList.remove('hidden');
        }
    }

    run();
})();
</script>
