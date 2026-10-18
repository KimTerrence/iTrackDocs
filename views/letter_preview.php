<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter Preview | <?= htmlspecialchars($tracking_id) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
    <link rel="stylesheet" href="assets/letter/letter-page.css">
    <style>
        html, body { margin: 0; background: #f1f5f9; font-family: system-ui, sans-serif; }
        .preview-wrap { padding: 16px; }
        .preview-scaler { margin: 0 auto; overflow: hidden; }
        .preview-scaler .letter-stack { transform-origin: top left; margin: 0; }
        .preview-empty { max-width: 420px; margin: 40px auto; text-align: center; color: #64748b; font-size: 14px; }

        /* Print at real size on legal paper: one sheet per page, no on-screen scaling or gaps */
        @media print {
            @page { size: 8.5in 13in; margin: 0; }
            html, body { background: #fff; }
            .preview-wrap { padding: 0; }
            .preview-scaler { width: auto !important; height: auto !important; overflow: visible; }
            .preview-scaler .letter-stack { transform: none !important; overflow: hidden; }
            .letter-sheet { box-shadow: none; }
        }
    </style>
</head>
<body>
    <?php if (!$hasLetter): ?>
        <p class="preview-empty">No letter has been saved for this document yet.</p>
    <?php else: ?>
        <div class="preview-wrap">
            <div class="preview-scaler" id="previewScaler">
                <div class="letter-stack" id="previewStack" data-code="<?= htmlspecialchars($tracking_id) ?>">
                    <div class="letter-body">
                        <div id="letterQuill" class="letter-quill"></div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script src="assets/letter/letter-page.js"></script>
        <script>
        (function () {
            const SAVED_CONTENT = <?= json_encode($letterContent, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
            const stack = document.getElementById('previewStack');
            const scaler = document.getElementById('previewScaler');
            let printing = false;

            // Lay out the sheets, shrink them to fit narrow frames, and tell the parent page how tall we are
            function fit() {
                if (printing) return;
                LetterPage.layout(stack);
                stack.style.transform = '';
                const scale = Math.min(1, (document.documentElement.clientWidth - 32) / stack.offsetWidth);
                stack.style.transform = 'scale(' + scale + ')';
                scaler.style.width = (stack.offsetWidth * scale) + 'px';
                scaler.style.height = (stack.offsetHeight * scale) + 'px';
                if (window.parent !== window) {
                    window.parent.postMessage({ type: 'letter-preview-height', height: document.documentElement.scrollHeight }, window.location.origin);
                }
            }

            // Sheets edge to edge, exactly one 13in page each (1px trimmed so no blank page follows)
            function beforePrint() {
                if (printing) return;
                printing = true;
                stack.style.transform = 'none';
                const pages = LetterPage.layout(stack, 0);
                stack.style.height = (pages * LetterPage.PAGE.height - 1) + 'px';
            }
            function afterPrint() {
                if (!printing) return;
                printing = false;
                fit();
            }

            LetterPage.registerSizes();
            const quill = new Quill('#letterQuill', { theme: 'snow', readOnly: true, modules: { toolbar: false } });
            if (SAVED_CONTENT) quill.setContents(SAVED_CONTENT, 'silent');

            LetterPage.loadTemplate()
                .then(bytes => LetterPage.showTemplateImages(bytes, stack))
                .catch(err => console.error('Letter template:', err))
                .finally(fit);
            if (document.fonts) document.fonts.ready.then(fit);
            window.addEventListener('resize', fit);
            window.addEventListener('beforeprint', beforePrint);
            window.addEventListener('afterprint', afterPrint);
            window.matchMedia('print').addEventListener('change', e => e.matches ? beforePrint() : afterPrint());
            fit();
        })();
        </script>
    <?php endif; ?>
</body>
</html>
