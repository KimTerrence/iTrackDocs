<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
<link rel="stylesheet" href="assets/letter/letter-page.css">
<style>
    #letterToolbar { border: 0 !important; border-bottom: 1px solid #e2e8f0 !important; background: #fff; }
    #letterToolbar .ql-picker.ql-size .ql-picker-label::before,
    #letterToolbar .ql-picker.ql-size .ql-picker-item::before { content: '12'; }
    <?php foreach (['10pt', '11pt', '14pt', '16pt', '18pt'] as $pt): ?>
    #letterToolbar .ql-picker.ql-size [data-value="<?= $pt ?>"]::before { content: '<?= (int) $pt ?>'; }
    <?php endforeach; ?>
</style>

<div class="w-full animate-fade-in-up space-y-4">

    <a href="index.php?action=view&tracking_id=<?= urlencode($tracking_id) ?>" class="inline-flex items-center text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-500 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Back to Document
    </a>

    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="min-w-0">
            <span class="font-mono text-brand-600 dark:text-brand-400 font-bold px-3 py-1 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-sm"><?= htmlspecialchars($tracking_id) ?></span>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-2 truncate"><?= htmlspecialchars($document['title']) ?></h1>
            <p id="letterStatus" class="text-xs text-slate-500 dark:text-slate-400 mt-1">Loading letter...</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button id="downloadBtn" type="button" disabled class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 transition-all disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Download .docx
            </button>
            <?php if ($canEdit): ?>
                <button id="saveBtn" type="button" disabled class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm rounded-xl shadow-md shadow-brand-500/20 transition-all disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Save Letter
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-slate-100 dark:bg-slate-950 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <?php if ($canEdit): ?>
            <div id="letterToolbar">
                <span class="ql-formats">
                    <select class="ql-size">
                        <option value="10pt"></option>
                        <option value="11pt"></option>
                        <option selected></option>
                        <option value="14pt"></option>
                        <option value="16pt"></option>
                        <option value="18pt"></option>
                    </select>
                </span>
                <span class="ql-formats">
                    <button class="ql-bold"></button>
                    <button class="ql-italic"></button>
                    <button class="ql-underline"></button>
                    <button class="ql-strike"></button>
                </span>
                <span class="ql-formats">
                    <select class="ql-align"></select>
                </span>
                <span class="ql-formats">
                    <button class="ql-list" value="ordered"></button>
                    <button class="ql-list" value="bullet"></button>
                    <button class="ql-indent" value="-1"></button>
                    <button class="ql-indent" value="+1"></button>
                </span>
                <span class="ql-formats">
                    <button class="ql-clean"></button>
                </span>
                <!-- Not a Quill format button -->
                <button id="toolbarSaveBtn" type="button" disabled style="float:right;width:auto;height:auto;" class="px-3 py-1 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-lg disabled:opacity-50">
                    Save
                </button>
            </div>
        <?php endif; ?>
        <!-- The page scrolls inside this box so the toolbar above it stays in view
             (the layout's <main> is overflow-hidden, which rules out position: sticky) -->
        <div id="letterScroll" class="overflow-auto p-3 sm:p-8" style="max-height: calc(100vh - 140px);">
            <!-- Sized to the scaled page so narrow screens show the whole width (see fitPage) -->
            <div id="letterFit" class="mx-auto overflow-hidden">
                <div class="letter-stack" id="letterStack" data-code="<?= htmlspecialchars($tracking_id) ?>" style="transform-origin: top left; margin: 0;">
                    <div class="letter-body">
                        <div id="letterQuill" class="letter-quill"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script src="assets/letter/letter-page.js"></script>
<script>
(function () {
    const TRACKING_ID = <?= json_encode($tracking_id) ?>;
    const CAN_EDIT = <?= json_encode($canEdit) ?>;
    const HAS_LETTER = <?= json_encode($hasLetter) ?>;
    const SAVED_CONTENT = <?= json_encode($letterContent, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    const LETTER_URL = 'index.php?action=letter_file&tracking_id=' + encodeURIComponent(TRACKING_ID);
    const DOCX_MIME = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    const { W_NS, SIZES, hasDrawing } = LetterPage;

    const statusEl = document.getElementById('letterStatus');
    const saveButtons = ['saveBtn', 'toolbarSaveBtn'].map(id => document.getElementById(id)).filter(Boolean);
    const downloadBtn = document.getElementById('downloadBtn');
    let quill = null;
    let templateBytes = null;
    let dirty = false;
    let saving = false;

    function setStatus(text, isError) {
        statusEl.textContent = text;
        statusEl.className = 'text-xs mt-1 ' + (isError ? 'text-red-600 font-semibold' : 'text-slate-500 dark:text-slate-400');
    }

    function escapeXml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // ---------- Quill content -> WordprocessingML paragraphs ----------

    function runXml(text, attrs) {
        let rPr = '';
        if (attrs.bold) rPr += '<w:b/><w:bCs/>';
        if (attrs.italic) rPr += '<w:i/><w:iCs/>';
        if (attrs.strike) rPr += '<w:strike/>';
        if (attrs.underline) rPr += '<w:u w:val="single"/>';
        if (attrs.size && SIZES.includes(attrs.size)) {
            const halfPoints = parseInt(attrs.size, 10) * 2;
            rPr += '<w:sz w:val="' + halfPoints + '"/><w:szCs w:val="' + halfPoints + '"/>';
        }
        const parts = text.split('\t').map(t => t ? '<w:t xml:space="preserve">' + escapeXml(t) + '</w:t>' : '');
        return '<w:r>' + (rPr ? '<w:rPr>' + rPr + '</w:rPr>' : '') + parts.join('<w:tab/>') + '</w:r>';
    }

    function deltaToParagraphs(delta) {
        // Split the delta into lines: text attributes style runs, the "\n" attributes style the paragraph
        const lines = [];
        let runs = [];
        (delta.ops || []).forEach(op => {
            if (typeof op.insert !== 'string') return; // embeds are not offered in the toolbar
            const pieces = op.insert.split('\n');
            pieces.forEach((piece, i) => {
                if (piece) runs.push(runXml(piece, op.attributes || {}));
                if (i < pieces.length - 1) {
                    lines.push({ runs: runs, attrs: op.attributes || {} });
                    runs = [];
                }
            });
        });
        if (runs.length) lines.push({ runs: runs, attrs: {} });

        const counters = {}; // ordered-list numbering per indent level
        return lines.map(line => {
            const a = line.attrs;
            const indent = parseInt(a.indent || 0, 10);
            const jc = { center: 'center', right: 'right', justify: 'both' }[a.align] || 'left';
            let ind = '<w:ind w:left="' + (720 * indent) + '" w:firstLine="0"/>';
            let prefix = '';

            if (a.list === 'ordered' || a.list === 'bullet') {
                ind = '<w:ind w:left="' + (720 * (indent + 1)) + '" w:hanging="360"/>';
                if (a.list === 'ordered') {
                    counters[indent] = (counters[indent] || 0) + 1;
                    prefix = runXml(counters[indent] + '.\t', {});
                } else {
                    prefix = runXml('•\t', {});
                }
                Object.keys(counters).forEach(k => { if (k > indent) delete counters[k]; });
            } else {
                Object.keys(counters).forEach(k => delete counters[k]);
            }

            return '<w:p><w:pPr><w:spacing w:after="0" w:line="259" w:lineRule="auto"/>' + ind + '<w:jc w:val="' + jc + '"/></w:pPr>'
                + prefix + line.runs.join('') + '</w:p>';
        });
    }

    // ---------- Build the .docx from Template.docx ----------

    // Namespaces used by generated markup (all declared on the template's <w:document> root too)
    const FRAGMENT_NS = {
        w: W_NS,
        wp: 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing',
        a: 'http://schemas.openxmlformats.org/drawingml/2006/main',
        wps: 'http://schemas.microsoft.com/office/word/2010/wordprocessingShape',
        mc: 'http://schemas.openxmlformats.org/markup-compatibility/2006',
        v: 'urn:schemas-microsoft-com:vml',
        o: 'urn:schemas-microsoft-com:office:office',
    };

    function parseFragment(xml) {
        const decls = Object.keys(FRAGMENT_NS).map(p => ' xmlns:' + p + '="' + FRAGMENT_NS[p] + '"').join('');
        const doc = new DOMParser().parseFromString('<w:root' + decls + '>' + xml + '</w:root>', 'application/xml');
        return doc.documentElement.firstElementChild;
    }

    // QR + tracking code sit in the top-right corner of the page, kept this far from the paper edges (EMU)
    const PAGE_WIDTH = 7772400; // 8.5in
    const CORNER_MARGIN = 182880; // 0.2in

    // Tracking code as a borderless text box right under the QR code at the top-right corner of the page,
    // right-aligned with the QR modules (the inset skips the QR's white border).
    // Same structure Word writes: DrawingML text box with a VML fallback for Word 2007.
    const CODE_BOX = { top: CORNER_MARGIN + 777240, width: 2011680, height: 228600, inset: 45720 }; // EMU (under the 0.85in QR, 2.2in x 0.25in, 0.05in right inset)
    CODE_BOX.left = PAGE_WIDTH - CORNER_MARGIN - CODE_BOX.width;

    function trackingCodeRunXml() {
        const content = '<w:txbxContent><w:p><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:ind w:left="0" w:firstLine="0"/><w:jc w:val="right"/></w:pPr>'
            + runXml(TRACKING_ID, { bold: true, size: '10pt' }) + '</w:p></w:txbxContent>';
        const pt = emu => (emu / 12700).toFixed(1) + 'pt';
        return '<w:r><w:rPr><w:noProof/></w:rPr><mc:AlternateContent><mc:Choice Requires="wps"><w:drawing>'
            + '<wp:anchor distT="0" distB="0" distL="0" distR="0" simplePos="0" relativeHeight="251661312" behindDoc="0" locked="1" layoutInCell="1" allowOverlap="1">'
            + '<wp:simplePos x="0" y="0"/>'
            + '<wp:positionH relativeFrom="page"><wp:posOffset>' + CODE_BOX.left + '</wp:posOffset></wp:positionH>'
            + '<wp:positionV relativeFrom="page"><wp:posOffset>' + CODE_BOX.top + '</wp:posOffset></wp:positionV>'
            + '<wp:extent cx="' + CODE_BOX.width + '" cy="' + CODE_BOX.height + '"/><wp:effectExtent l="0" t="0" r="0" b="0"/><wp:wrapNone/>'
            + '<wp:docPr id="5001" name="Tracking Code"/><wp:cNvGraphicFramePr/>'
            + '<a:graphic xmlns:a="' + FRAGMENT_NS.a + '"><a:graphicData uri="http://schemas.microsoft.com/office/word/2010/wordprocessingShape"><wps:wsp>'
            + '<wps:cNvSpPr txBox="1"/>'
            + '<wps:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="' + CODE_BOX.width + '" cy="' + CODE_BOX.height + '"/></a:xfrm>'
            + '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom><a:noFill/><a:ln><a:noFill/></a:ln></wps:spPr>'
            + '<wps:txbx>' + content + '</wps:txbx>'
            + '<wps:bodyPr rot="0" vert="horz" wrap="square" lIns="0" tIns="0" rIns="' + CODE_BOX.inset + '" bIns="0" anchor="ctr" anchorCtr="0"><a:noAutofit/></wps:bodyPr>'
            + '</wps:wsp></a:graphicData></a:graphic></wp:anchor></w:drawing></mc:Choice>'
            + '<mc:Fallback><w:pict><v:shape id="TrackingCode" type="#_x0000_t202" filled="f" stroked="f" style="position:absolute;margin-left:' + pt(CODE_BOX.left) + ';margin-top:' + pt(CODE_BOX.top)
            + ';width:' + pt(CODE_BOX.width) + ';height:' + pt(CODE_BOX.height) + ';z-index:251661312;mso-position-horizontal-relative:page;mso-position-vertical-relative:page">'
            + '<v:textbox inset="0,0,' + pt(CODE_BOX.inset) + ',0">' + content + '</v:textbox></v:shape></w:pict></mc:Fallback></mc:AlternateContent></w:r>';
    }

    // QR code (links to the public scan page) as a picture in the top-right corner of the page,
    // above the tracking code. Its PNG is added to the package as word/media/itd-qr.png.
    const QR_BOX = { top: CORNER_MARGIN, size: 777240 }; // EMU (0.85in square, so the code under it clears the letterhead line)
    QR_BOX.left = PAGE_WIDTH - CORNER_MARGIN - QR_BOX.size;
    const QR_REL = { id: 'rIdItdQr', target: 'media/itd-qr.png' };

    function qrRunXml() {
        const ext = 'cx="' + QR_BOX.size + '" cy="' + QR_BOX.size + '"';
        return '<w:r><w:rPr><w:noProof/></w:rPr><w:drawing>'
            + '<wp:anchor distT="0" distB="0" distL="0" distR="0" simplePos="0" relativeHeight="251662336" behindDoc="0" locked="1" layoutInCell="1" allowOverlap="1">'
            + '<wp:simplePos x="0" y="0"/>'
            + '<wp:positionH relativeFrom="page"><wp:posOffset>' + QR_BOX.left + '</wp:posOffset></wp:positionH>'
            + '<wp:positionV relativeFrom="page"><wp:posOffset>' + QR_BOX.top + '</wp:posOffset></wp:positionV>'
            + '<wp:extent ' + ext + '/><wp:effectExtent l="0" t="0" r="0" b="0"/><wp:wrapNone/>'
            + '<wp:docPr id="5002" name="Tracking QR"/><wp:cNvGraphicFramePr/>'
            + '<a:graphic xmlns:a="' + FRAGMENT_NS.a + '"><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
            + '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">'
            + '<pic:nvPicPr><pic:cNvPr id="5002" name="Tracking QR"/><pic:cNvPicPr><a:picLocks noChangeAspect="1"/></pic:cNvPicPr></pic:nvPicPr>'
            + '<pic:blipFill><a:blip r:embed="' + QR_REL.id + '"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>'
            + '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext ' + ext + '/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>'
            + '</pic:pic></a:graphicData></a:graphic></wp:anchor></w:drawing></w:r>';
    }

    const R_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    const REL_TYPE = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/';
    const XML_DECL = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\n';
    const HEADER_PART = { name: 'itd-header.xml', relId: 'rIdItdHeader', type: 'header', tag: 'w:hdr', mime: 'application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml' };
    const FOOTER_PART = { name: 'itd-footer.xml', relId: 'rIdItdFooter', type: 'footer', tag: 'w:ftr', mime: 'application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml' };

    // Word header/footer part holding the given runs, plus its relationships for the images it uses
    // (template images from docRels, plus any extra <Relationship> markup)
    function addHeaderFooterPart(zip, part, rootAttrs, runsXml, docRels, extraRels) {
        zip.file('word/' + part.name, XML_DECL + '<' + part.tag + rootAttrs + '><w:p><w:pPr><w:spacing w:after="0"/></w:pPr>' + runsXml + '</w:p></' + part.tag + '>');

        const usedIds = Array.from(runsXml.matchAll(/r:embed="([^"]+)"/g)).map(m => m[1]);
        const rels = Array.from(docRels.getElementsByTagName('Relationship'))
            .filter(r => usedIds.includes(r.getAttribute('Id')))
            .map(r => '<Relationship Id="' + r.getAttribute('Id') + '" Type="' + r.getAttribute('Type') + '" Target="' + r.getAttribute('Target') + '"/>');
        zip.file('word/_rels/' + part.name + '.rels', XML_DECL + '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' + rels.join('') + (extraRels || '') + '</Relationships>');
    }

    // Builds the letter from Template.docx: the template's letterhead + tracking code go into a Word
    // header and its footer image into a Word footer, so every page repeats them; the body is the letter text
    async function buildDocx(delta) {
        const zip = await JSZip.loadAsync(templateBytes);
        const docXml = await zip.file('word/document.xml').async('string');
        const rootStart = docXml.indexOf('<w:document');
        const rootAttrs = docXml.slice(rootStart + '<w:document'.length, docXml.indexOf('>', rootStart));
        const doc = new DOMParser().parseFromString(docXml, 'application/xml');
        const body = doc.getElementsByTagNameNS(W_NS, 'body')[0];
        const children = Array.from(body.children);
        const sectPr = children.find(el => el.localName === 'sectPr');
        const blocks = children.filter(el => el !== sectPr);
        const headerPara = hasDrawing(blocks[0]) ? blocks[0] : null;
        const footerPara = blocks.length > 1 && hasDrawing(blocks[blocks.length - 1]) ? blocks[blocks.length - 1] : null;

        // Image runs (anchored to the page) move from the template body into the header/footer parts
        const serializer = new XMLSerializer();
        const drawingRuns = p => p ? Array.from(p.children)
            .filter(r => r.localName === 'r' && r.getElementsByTagNameNS(W_NS, 'drawing').length)
            .map(r => serializer.serializeToString(r)).join('') : '';
        const docRels = new DOMParser().parseFromString(await zip.file('word/_rels/document.xml.rels').async('string'), 'application/xml');
        const qrCanvas = LetterPage.qrCanvas(LetterPage.scanUrl(TRACKING_ID), 10);
        zip.file('word/' + QR_REL.target, await new Promise(resolve => qrCanvas.toBlob(resolve, 'image/png')));
        addHeaderFooterPart(zip, HEADER_PART, rootAttrs, drawingRuns(headerPara) + trackingCodeRunXml() + qrRunXml(), docRels,
            '<Relationship Id="' + QR_REL.id + '" Type="' + REL_TYPE + 'image" Target="' + QR_REL.target + '"/>');
        const hasFooter = !!footerPara;
        if (hasFooter) addHeaderFooterPart(zip, FOOTER_PART, rootAttrs, drawingRuns(footerPara), docRels);

        // Body: only the letter text
        blocks.forEach(el => body.removeChild(el));
        const paragraphs = deltaToParagraphs(delta);
        (paragraphs.length ? paragraphs : ['<w:p/>']).forEach(xml => {
            body.insertBefore(doc.importNode(parseFragment(xml), true), sectPr || null);
        });

        // Section: use the header/footer on every page; margins keep the text clear of them
        if (sectPr) {
            Array.from(sectPr.children).filter(el => el.localName === 'headerReference' || el.localName === 'footerReference').forEach(el => sectPr.removeChild(el));
            const refs = [[HEADER_PART, 'w:headerReference']].concat(hasFooter ? [[FOOTER_PART, 'w:footerReference']] : []);
            refs.reverse().forEach(([part, tag]) => {
                const ref = doc.createElementNS(W_NS, tag);
                ref.setAttributeNS(W_NS, 'w:type', 'default');
                ref.setAttributeNS(R_NS, 'r:id', part.relId);
                sectPr.insertBefore(ref, sectPr.firstChild);
            });
            const pgMar = sectPr.getElementsByTagNameNS(W_NS, 'pgMar')[0];
            if (pgMar) {
                pgMar.setAttributeNS(W_NS, 'w:top', String(LetterPage.MARGIN_TWIPS.top));
                pgMar.setAttributeNS(W_NS, 'w:bottom', String(LetterPage.MARGIN_TWIPS.bottom));
            }
        }
        zip.file('word/document.xml', serializer.serializeToString(doc));

        // Register the new parts
        let relsXml = await zip.file('word/_rels/document.xml.rels').async('string');
        [HEADER_PART].concat(hasFooter ? [FOOTER_PART] : []).forEach(part => {
            relsXml = relsXml.replace('</Relationships>', '<Relationship Id="' + part.relId + '" Type="' + REL_TYPE + part.type + '" Target="' + part.name + '"/></Relationships>');
        });
        zip.file('word/_rels/document.xml.rels', relsXml);

        let typesXml = await zip.file('[Content_Types].xml').async('string');
        [HEADER_PART].concat(hasFooter ? [FOOTER_PART] : []).forEach(part => {
            typesXml = typesXml.replace('</Types>', '<Override PartName="/word/' + part.name + '" ContentType="' + part.mime + '"/></Types>');
        });
        zip.file('[Content_Types].xml', typesXml);

        return zip.generateAsync({ type: 'blob', mimeType: DOCX_MIME });
    }

    // ---------- Save / download ----------

    async function save() {
        if (saving) return;
        saving = true;
        saveButtons.forEach(b => b.disabled = true);
        setStatus('Saving...');
        try {
            const delta = quill.getContents();
            const form = new FormData();
            form.append('tracking_id', TRACKING_ID);
            form.append('content', JSON.stringify(delta));
            form.append('letter', await buildDocx(delta), TRACKING_ID + '.docx');

            const res = await fetch('index.php?action=save_letter', { method: 'POST', body: form, credentials: 'same-origin' });
            const data = await res.json().catch(() => ({ error: 'Unexpected server response.' }));
            if (!res.ok || !data.ok) throw new Error(data.error || 'Save failed.');

            dirty = false;
            setStatus('Saved at ' + data.saved_at);
        } catch (err) {
            setStatus('Save failed: ' + err.message, true);
        } finally {
            saving = false;
            saveButtons.forEach(b => b.disabled = false);
        }
    }

    async function download() {
        if (CAN_EDIT && (dirty || !HAS_LETTER)) {
            await save();
            if (dirty) return; // save failed, status already shows why
        }
        window.location.href = LETTER_URL + '&download=1';
    }

    async function init() {
        LetterPage.registerSizes();

        quill = new Quill('#letterQuill', {
            theme: 'snow',
            readOnly: !CAN_EDIT,
            placeholder: CAN_EDIT ? 'Start typing your letter...' : '',
            modules: { toolbar: CAN_EDIT ? '#letterToolbar' : false },
        });
        if (SAVED_CONTENT) quill.setContents(SAVED_CONTENT, 'silent');

        // Re-flow the text over legal-size sheets whenever it changes
        const stack = document.getElementById('letterStack');
        let layoutQueued = false;
        const scheduleLayout = () => {
            if (layoutQueued) return;
            layoutQueued = true;
            requestAnimationFrame(() => { layoutQueued = false; LetterPage.layout(stack); });
        };
        quill.on('text-change', scheduleLayout);
        LetterPage.layout(stack);
        if (document.fonts) document.fonts.ready.then(scheduleLayout);

        // Shrink the 816px-wide pages to the screen width on phones/tablets (typing still works when scaled)
        const scroller = document.getElementById('letterScroll');
        const fitBox = document.getElementById('letterFit');
        const fitPage = () => {
            const padding = parseFloat(getComputedStyle(scroller).paddingLeft) * 2;
            const scale = Math.min(1, (scroller.clientWidth - padding) / stack.offsetWidth);
            stack.style.transform = scale < 1 ? 'scale(' + scale + ')' : '';
            fitBox.style.width = (stack.offsetWidth * scale) + 'px';
            fitBox.style.height = (stack.offsetHeight * scale) + 'px';
        };
        // Page count (stack height) and screen width both change the fit
        new ResizeObserver(fitPage).observe(stack);
        new ResizeObserver(fitPage).observe(scroller);
        fitPage();

        try {
            templateBytes = await LetterPage.loadTemplate();
            await LetterPage.showTemplateImages(templateBytes, stack);
        } catch (err) {
            setStatus('Could not load the letter template: ' + err.message, true);
            quill.enable(false);
            return;
        }

        downloadBtn.disabled = !CAN_EDIT && !HAS_LETTER;
        if (!CAN_EDIT) {
            setStatus(HAS_LETTER ? 'View only.' : 'View only. No letter has been saved yet.');
            return;
        }

        quill.on('text-change', function () {
            dirty = true;
            setStatus('Unsaved changes');
        });
        saveButtons.forEach(b => b.disabled = false);
        setStatus('Type the letter below, then click Save Letter.');
        if (!HAS_LETTER) save(); // store the starting letter (template + code) right away
        quill.focus();
    }

    saveButtons.forEach(b => b.addEventListener('click', save));
    downloadBtn.addEventListener('click', download);
    document.addEventListener('keydown', function (e) {
        if (CAN_EDIT && (e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') { e.preventDefault(); save(); }
    });
    window.addEventListener('beforeunload', function (e) {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });

    init();
})();
</script>
