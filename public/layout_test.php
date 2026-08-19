<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_department'] = 'CICS';
$_SESSION['user_role'] = 'admin';

$tracking_id = 'IC26060001';
$creator = ['name' => 'John Doe', 'department' => 'CICS', 'email' => 'john@example.com'];

$document = [
    'tracking_id' => 'IC26060001',
    'title' => 'Test Document Title',
    'category' => 'IC',
    'origin' => 'CICS',
    'target_department' => 'CICS',
    'current_location' => 'CICS',
    'status' => 'Routing',
    'file_path' => null,
    'creator_id' => 1,
    'created_at' => '2026-06-15 15:37:38',
    'history' => [
        ['date' => 'Jun 15, 2026 3:37 PM', 'action' => 'Document created. Target destination: CICS'],
        ['date' => 'Jun 16, 2026 9:00 AM', 'action' => 'Document in routing to CICS'],
    ]
];

ob_start();
require __DIR__ . '/../views/view.php';
$content = ob_get_clean();

// Render layout but override the end of body to include layout inspection
ob_start();
require __DIR__ . '/../views/layout.php';
$html = ob_get_clean();

// Inject layout inspection script before </body>
$inspectScript = '
<script>
setTimeout(function() {
    var sidebar = document.querySelector(".w-64");
    var leftCol = document.querySelector(".flex-1.min-w-0.space-y-6");
    var flexRow = document.querySelector(".flex.flex-row.flex-nowrap");
    var main = document.querySelector("main");
    var info = "=== LAYOUT DEBUG ===\\n";
    if (flexRow) {
        var rect = flexRow.getBoundingClientRect();
        info += "Flex Row: left=" + Math.round(rect.left) + " top=" + Math.round(rect.top) + " width=" + Math.round(rect.width) + " height=" + Math.round(rect.height) + "\\n";
    }
    if (leftCol) {
        var rect = leftCol.getBoundingClientRect();
        info += "Left Column: left=" + Math.round(rect.left) + " top=" + Math.round(rect.top) + " width=" + Math.round(rect.width) + " height=" + Math.round(rect.height) + "\\n";
    }
    if (sidebar) {
        var rect = sidebar.getBoundingClientRect();
        info += "Right Sidebar: left=" + Math.round(rect.left) + " top=" + Math.round(rect.top) + " width=" + Math.round(rect.width) + " height=" + Math.round(rect.height) + "\\n";
    }
        if (main) {
        var rect = main.getBoundingClientRect();
        info += "Main Element: left=" + Math.round(rect.left) + " top=" + Math.round(rect.top) + " width=" + Math.round(rect.width) + " height=" + Math.round(rect.height) + " overflow=" + getComputedStyle(main).overflow + "\\n";
    }
    if (flexRow) {
        info += "Flex Row computed display: " + getComputedStyle(flexRow).display + "\\n";
        info += "Flex Row computed flexDirection: " + getComputedStyle(flexRow).flexDirection + "\\n";
        info += "Flex Row computed flexWrap: " + getComputedStyle(flexRow).flexWrap + "\\n";
        info += "Flex Row childNodes: " + flexRow.children.length + "\\n";
        info += "Flex Row innerHTML length: " + flexRow.innerHTML.length + "\\n";
        info += "Flex Row innerHTML (first 500 chars): " + flexRow.innerHTML.substring(0, 500) + "\\n";
    }
    if (sidebar) {
        info += "Sidebar computed display: " + getComputedStyle(sidebar).display + "\\n";
        info += "Sidebar computed position: " + getComputedStyle(sidebar).position + "\\n";
        info += "Sidebar computed flexGrow: " + getComputedStyle(sidebar).flexGrow + "\\n";
        info += "Sidebar computed flexShrink: " + getComputedStyle(sidebar).flexShrink + "\\n";
        info += "Sidebar computed flexBasis: " + getComputedStyle(sidebar).flexBasis + "\\n";
        info += "Sidebar parent tag: " + sidebar.parentElement.tagName + "\\n";
        info += "Sidebar parent class: " + sidebar.parentElement.className + "\\n";
    }
    document.documentElement.style.outline = "2px solid red";
    if (sidebar) sidebar.style.outline = "2px solid blue";
    if (leftCol) leftCol.style.outline = "2px solid green";
    var div = document.createElement("div");
    div.style.cssText = "position:fixed;top:0;left:0;z-index:9999;background:yellow;padding:10px;font-family:monospace;font-size:12px;maxWidth:400px;whiteSpace:pre;";
    div.textContent = info;
    document.body.appendChild(div);
    console.log(info);
}, 2000);
</script>
';
$html = str_replace('</body>', $inspectScript . '</body>', $html);
echo $html;

