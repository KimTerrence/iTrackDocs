// Shared by the letter editor and the letter preview (needs JSZip and Quill loaded first)
window.LetterPage = (function () {
    const W_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    const A_NS = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    const R_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    const SIZES = ['10pt', '11pt', '14pt', '16pt', '18pt'];

    // Page geometry in px (96/in), matching the .docx built by the editor:
    //  - legal page 8.5in x 13in
    //  - every page repeats the template's letterhead (1.69in) and footer (starts 11.72in down)
    //  - text runs between them: top margin 2790 twips (1.94in), bottom margin 1845 twips (1.28in)
    const PAGE = { height: 1248, top: 186, bottom: 1125, footerTop: 1126 };
    const MARGIN_TWIPS = { top: 2790, bottom: 1845 };
    const SCREEN_GAP = 24;

    function hasDrawing(el) {
        return !!el && el.localName === 'p' && el.getElementsByTagNameNS(W_NS, 'drawing').length > 0;
    }

    // Font sizes are stored as inline styles (e.g. 14pt) so they map straight to Word sizes
    function registerSizes() {
        const Size = Quill.import('attributors/style/size');
        Size.whitelist = SIZES;
        Quill.register(Size, true);
    }

    // Public scan page for a tracking code (same link as the document's printed QR)
    function scanUrl(code) {
        return window.location.origin + window.location.pathname + '?action=scan&tracking_id=' + encodeURIComponent(code);
    }

    // QR code as a canvas, `scale` px per module, with a 2-module white border (needs qrcode-generator)
    function qrCanvas(text, scale) {
        const qr = qrcode(0, 'L');
        qr.addData(text);
        qr.make();
        const quiet = 2;
        const count = qr.getModuleCount();
        const canvas = document.createElement('canvas');
        canvas.width = canvas.height = (count + quiet * 2) * scale;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000';
        for (let r = 0; r < count; r++) {
            for (let c = 0; c < count; c++) {
                if (qr.isDark(r, c)) ctx.fillRect((c + quiet) * scale, (r + quiet) * scale, scale, scale);
            }
        }
        return canvas;
    }

    async function loadTemplate(url) {
        const res = await fetch(url || 'Template.docx', { cache: 'no-store' });
        if (!res.ok) throw new Error('Template.docx not found (' + res.status + ')');
        return res.arrayBuffer();
    }

    // The template's letterhead (first paragraph image) and footer (last paragraph image) as blob URLs
    async function templateImages(templateBytes) {
        const zip = await JSZip.loadAsync(templateBytes);
        const doc = new DOMParser().parseFromString(await zip.file('word/document.xml').async('string'), 'application/xml');
        const rels = new DOMParser().parseFromString(await zip.file('word/_rels/document.xml.rels').async('string'), 'application/xml');
        const blocks = Array.from(doc.getElementsByTagNameNS(W_NS, 'body')[0].children).filter(el => el.localName === 'p');

        async function imageUrl(p) {
            const blip = hasDrawing(p) ? p.getElementsByTagNameNS(A_NS, 'blip')[0] : null;
            const relId = blip && blip.getAttributeNS(R_NS, 'embed');
            const rel = relId && Array.from(rels.getElementsByTagName('Relationship')).find(r => r.getAttribute('Id') === relId);
            const file = rel && zip.file('word/' + rel.getAttribute('Target'));
            return file ? URL.createObjectURL(await file.async('blob')) : null;
        }

        return {
            header: await imageUrl(blocks[0]),
            footer: blocks.length > 1 ? await imageUrl(blocks[blocks.length - 1]) : null,
        };
    }

    // Shows the letterhead/footer on every sheet of this stack (re-runs the layout)
    async function showTemplateImages(templateBytes, stack) {
        stack._letterImages = await templateImages(templateBytes);
        layout(stack, stack._letterGap);
        const imgs = Array.from(stack.querySelectorAll('.letter-sheet img'));
        await Promise.all(imgs.map(img => img.complete ? null : new Promise(r => { img.onload = img.onerror = r; })));
    }

    function buildSheet(stack, top) {
        const sheet = document.createElement('div');
        sheet.className = 'letter-sheet';
        sheet.style.top = top + 'px';
        const images = stack._letterImages || {};
        if (images.header) {
            const img = document.createElement('img');
            img.className = 'letter-headimg';
            img.alt = '';
            img.src = images.header;
            sheet.appendChild(img);
        }
        const code = document.createElement('div');
        code.className = 'letter-code';
        code.textContent = stack.dataset.code || '';
        sheet.appendChild(code);
        if (stack.dataset.code && typeof qrcode === 'function') {
            if (!stack._letterQr) stack._letterQr = qrCanvas(scanUrl(stack.dataset.code), 8).toDataURL('image/png');
            const qr = document.createElement('img');
            qr.className = 'letter-qr';
            qr.alt = 'Tracking QR';
            qr.src = stack._letterQr;
            sheet.appendChild(qr);
        }
        if (images.footer) {
            const img = document.createElement('img');
            img.className = 'letter-footimg';
            img.alt = '';
            img.src = images.footer;
            sheet.appendChild(img);
        }
        return sheet;
    }

    // Push paragraphs that would cross a page bottom onto the next sheet, then draw the sheets.
    // Uses a <style> element (nth-child margins) so Quill's own DOM is never touched.
    // Note: a paragraph is moved as a whole; Word can split a long paragraph across pages.
    function layout(stack, gap) {
        gap = gap === undefined ? SCREEN_GAP : gap;
        stack._letterGap = gap;
        const editor = stack.querySelector('.ql-editor');
        const scope = '#' + stack.id + ' .ql-editor';
        let style = stack.querySelector('style.letter-pagination');
        if (!style) {
            style = document.createElement('style');
            style.className = 'letter-pagination';
            stack.appendChild(style);
        }
        style.textContent = '';

        const units = [];
        Array.from(editor.children).forEach((child, i) => {
            if (child.tagName === 'OL' || child.tagName === 'UL') {
                Array.from(child.children).forEach((li, j) => units.push({ el: li, sel: scope + ' > :nth-child(' + (i + 1) + ') > :nth-child(' + (j + 1) + ')' }));
            } else {
                units.push({ el: child, sel: scope + ' > :nth-child(' + (i + 1) + ')' });
            }
        });

        const pageTop = p => p * (PAGE.height + gap) + PAGE.top;
        const pageBottom = p => p * (PAGE.height + gap) + PAGE.bottom;
        const rules = [];
        let page = 0;
        let shift = 0;
        let firstOnPage = true;

        units.forEach(u => {
            const top = PAGE.top + u.el.offsetTop + shift;
            const height = u.el.offsetHeight;
            if (!firstOnPage && top + height > pageBottom(page)) {
                page++;
                const extra = pageTop(page) - top;
                rules.push(u.sel + '{margin-top:' + extra + 'px}');
                shift += extra;
            }
            firstOnPage = false;
            // A single paragraph taller than a page runs on over the following sheets
            while (PAGE.top + u.el.offsetTop + shift + height > pageBottom(page)) page++;
        });

        const pages = page + 1;
        // Keep the editable area down to the last page's text bottom so clicks there place the cursor
        rules.push(scope + '{min-height:' + (pageBottom(page) - PAGE.top) + 'px}');
        style.textContent = rules.join('\n');

        let sheets = stack.querySelector('.letter-sheets');
        if (!sheets) {
            sheets = document.createElement('div');
            sheets.className = 'letter-sheets';
            stack.insertBefore(sheets, stack.firstChild);
        }
        sheets.innerHTML = '';
        for (let p = 0; p < pages; p++) {
            sheets.appendChild(buildSheet(stack, p * (PAGE.height + gap)));
        }
        stack.style.height = (pages * PAGE.height + (pages - 1) * gap) + 'px';
        return pages;
    }

    return { W_NS, SIZES, PAGE, MARGIN_TWIPS, hasDrawing, registerSizes, scanUrl, qrCanvas, loadTemplate, showTemplateImages, layout };
})();
