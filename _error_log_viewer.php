<!DOCTYPE html>
<html>
<head>
    <title>Apache Error Log Viewer</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #569cd6; }
        .log-entry { background: #252526; border-left: 3px solid #569cd6; margin: 5px 0; padding: 8px; white-space: pre-wrap; word-break: break-word; font-size: 12px; }
        .error { border-left-color: #f48771; }
        .warning { border-left-color: #dcdcaa; }
        .csv-import { border-left-color: #4ec9b0; }
        .insert { border-left-color: #9cdcfe; }
        button { background: #569cd6; color: white; border: none; padding: 10px 15px; cursor: pointer; margin: 10px 5px 10px 0; font-size: 14px; }
        button:hover { background: #6ba3d4; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Apache Error Log Viewer</h1>
        <p><button onclick="location.reload()">↻ Refresh</button> <button onclick="autoRefresh()">⟲ Auto-refresh (5s)</button></p>
        <div id="logContent"></div>
    </div>

    <script>
        function loadLog() {
            fetch('_get_error_log.php')
                .then(r => r.text())
                .then(text => {
                    const div = document.getElementById('logContent');
                    const lines = text.split('\n').reverse(); // newest first
                    div.innerHTML = lines.slice(0, 200).map(line => {
                        let cls = '';
                        if (line.includes('CSV Import')) cls = 'csv-import';
                        else if (line.includes('insertWithFallback')) cls = 'insert';
                        else if (line.includes('ERROR') || line.includes('error')) cls = 'error';
                        else if (line.includes('WARNING') || line.includes('warning')) cls = 'warning';
                        return '<div class="log-entry ' + cls + '">' + escapeHtml(line) + '</div>';
                    }).join('');
                });
        }

        function escapeHtml(t) { return t.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
        function autoRefresh() { setInterval(loadLog, 5000); loadLog(); }
        loadLog();
    </script>
</body>
</html>
