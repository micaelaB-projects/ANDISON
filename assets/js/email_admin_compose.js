(function () {
    if (window.__andisonEmailComposeInitialized) return;
    window.__andisonEmailComposeInitialized = true;

    var ADMIN_EMAIL = 'ask_us@andisonindustrial.com';
    var API_RELATIVE_PATH = 'Andison/api/send_admin_email.php';
    var EMAIL_API_URL = resolveEmailApiUrl();

    function resolveEmailApiUrl() {
        var script = document.currentScript;

        if (!script || !script.src) {
            var scripts = document.getElementsByTagName('script');
            for (var i = scripts.length - 1; i >= 0; i--) {
                if (scripts[i].src && scripts[i].src.indexOf('email_admin_compose.js') !== -1) {
                    script = scripts[i];
                    break;
                }
            }
        }

        if (!script || !script.src) {
            return window.location.origin + '/' + API_RELATIVE_PATH;
        }

        try {
            var scriptUrl = new URL(script.src, window.location.href);
            var scriptPath = scriptUrl.pathname || '';
            var marker = '/assets/js/';
            var markerIndex = scriptPath.toLowerCase().indexOf(marker);
            var basePath = markerIndex >= 0 ? scriptPath.substring(0, markerIndex + 1) : '/';
            return scriptUrl.origin + basePath + API_RELATIVE_PATH;
        } catch (err) {
            return window.location.origin + '/' + API_RELATIVE_PATH;
        }
    }

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function sendEmailToServer(payload) {
        if (typeof window.fetch !== 'function') {
            return Promise.reject(new Error('Direct send is not supported in this browser.'));
        }

        var isFormData = typeof FormData !== 'undefined' && payload instanceof FormData;
        var headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (!isFormData) {
            headers['Content-Type'] = 'application/json';
        }

        return fetch(EMAIL_API_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: headers,
            body: isFormData ? payload : JSON.stringify(payload)
        }).then(function (response) {
            return response.text().then(function (text) {
                var data = null;
                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (err) {
                        data = null;
                    }
                }

                if (!response.ok || !data || data.success !== true) {
                    var errorMessage =
                        (data && data.message) ||
                        'Unable to send your email right now. Please try again.';
                    throw new Error(errorMessage);
                }

                return data;
            });
        });
    }

    function ensureStyles() {
        if (document.getElementById('emailComposeStyles')) return;

        var style = document.createElement('style');
        style.id = 'emailComposeStyles';
        style.textContent = '' +
            '#emailComposeOverlay{position:fixed;inset:0;background:rgba(15,18,35,.48);backdrop-filter:blur(3px);z-index:10050;display:none;align-items:center;justify-content:center;padding:16px;}' +
            '#emailComposeOverlay.open{display:flex;}' +
            '#emailComposeDialog{width:min(680px,96vw);background:#fff;border-radius:16px;box-shadow:0 24px 56px rgba(19,23,66,.35);overflow:hidden;font-family:"Google Sans",Roboto,"Helvetica Neue",Arial,sans-serif;-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;}' +
            '#emailComposeHeader{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#f5f7ff;border-bottom:1px solid #e5e9ff;}' +
            '#emailComposeTitle{margin:0;font-size:18px;font-weight:700;color:#1b1f47;letter-spacing:.1px;font-family:"Google Sans",Roboto,"Helvetica Neue",Arial,sans-serif;}' +
            '#emailComposeClose{border:none;background:#eceffd;color:#3a4385;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:20px;line-height:1;display:inline-flex;align-items:center;justify-content:center;}' +
            '#emailComposeForm{padding:16px 18px 18px;display:grid;gap:12px;}' +
            '.email-compose-row{display:grid;grid-template-columns:78px 1fr;align-items:center;gap:10px;}' +
            '.email-compose-label{font-size:12px;font-weight:600;color:#424b80;text-transform:uppercase;letter-spacing:.6px;font-family:"Google Sans",Roboto,"Helvetica Neue",Arial,sans-serif;}' +
            '.email-compose-input,.email-compose-textarea{width:100%;border:1px solid #d6dcff;border-radius:10px;padding:11px 12px;font-size:14px;color:#1d2245;background:#fff;font-family:Roboto,"Helvetica Neue",Arial,sans-serif;letter-spacing:.1px;}' +
            '.email-compose-input::placeholder,.email-compose-textarea::placeholder{color:#6f769a;font-family:Roboto,"Helvetica Neue",Arial,sans-serif;}' +
            '.email-compose-input:focus,.email-compose-textarea:focus{outline:none;border-color:#2B11DB;box-shadow:0 0 0 3px rgba(43,17,219,.12);}' +
            '.email-compose-input[readonly]{background:#f6f7fb;color:#5a628f;}' +
            '.email-compose-textarea{min-height:190px;resize:vertical;line-height:1.55;}' +
            '#emailComposeHint{padding:0 18px 6px;font-size:12px;color:#626a93;}' +
            '#emailComposeStatus{padding:0 18px 8px;min-height:20px;font-size:13px;font-weight:700;}' +
            '#emailComposeStatus.success{color:#0f7a4d;}' +
            '#emailComposeStatus.error{color:#c62828;}' +
            '#emailComposeStatus.info{color:#3146a0;}' +
            '#emailComposeFooter{display:flex;justify-content:flex-end;gap:10px;padding:0 18px 18px;flex-wrap:wrap;}' +
            '.email-compose-btn{border:none;border-radius:10px;padding:11px 16px;font-size:14px;font-weight:600;cursor:pointer;font-family:"Google Sans",Roboto,"Helvetica Neue",Arial,sans-serif;letter-spacing:.1px;}' +
            '.email-compose-btn.cancel{background:#eef1fb;color:#39427e;}' +
            '.email-compose-btn.send{background:linear-gradient(135deg,#14b86f 0%,#0f9468 100%);color:#fff;}' +
            '.email-compose-btn.send[disabled]{opacity:.68;cursor:not-allowed;}' +
            '@media (max-width:640px){.email-compose-row{grid-template-columns:1fr;gap:6px;}#emailComposeForm{gap:10px;}}';

        document.head.appendChild(style);
    }

    function ensureModal() {
        ensureStyles();

        var overlay = document.getElementById('emailComposeOverlay');
        if (overlay) {
            var existingNameLabel = overlay.querySelector('label[for="emailComposeName"]');
            if (existingNameLabel) {
                existingNameLabel.textContent = 'Company Name/Your Name';
            }
            var existingNameInput = document.getElementById('emailComposeName');
            if (existingNameInput) {
                existingNameInput.setAttribute('placeholder', 'Company name/your name');
            }

            return {
                overlay: overlay,
                form: document.getElementById('emailComposeForm'),
                closeBtn: document.getElementById('emailComposeClose'),
                cancelBtn: document.getElementById('emailComposeCancel'),
                sendBtn: document.getElementById('emailComposeSend'),
                status: document.getElementById('emailComposeStatus'),
                to: document.getElementById('emailComposeTo'),
                subject: document.getElementById('emailComposeSubject'),
                name: document.getElementById('emailComposeName'),
                email: document.getElementById('emailComposeEmail'),
                message: document.getElementById('emailComposeMessage')
            };
        }

        overlay = document.createElement('div');
        overlay.id = 'emailComposeOverlay';
        overlay.setAttribute('aria-hidden', 'true');
        overlay.innerHTML = '' +
            '<div id="emailComposeDialog" role="dialog" aria-modal="true" aria-labelledby="emailComposeTitle">' +
                '<div id="emailComposeHeader">' +
                    '<h3 id="emailComposeTitle">New Message</h3>' +
                    '<button id="emailComposeClose" type="button" aria-label="Close">&times;</button>' +
                '</div>' +
                '<form id="emailComposeForm">' +
                    '<div class="email-compose-row">' +
                        '<label class="email-compose-label" for="emailComposeTo">To</label>' +
                        '<input id="emailComposeTo" class="email-compose-input" type="text" readonly>' +
                    '</div>' +
                    '<div class="email-compose-row">' +
                        '<label class="email-compose-label" for="emailComposeSubject">Subject</label>' +
                        '<input id="emailComposeSubject" class="email-compose-input" type="text" maxlength="180" required>' +
                    '</div>' +
                    '<div class="email-compose-row">' +
                        '<label class="email-compose-label" for="emailComposeName">Company Name/Your Name</label>' +
                        '<input id="emailComposeName" class="email-compose-input" type="text" maxlength="120" placeholder="Company name/your name">' +
                    '</div>' +
                    '<div class="email-compose-row">' +
                        '<label class="email-compose-label" for="emailComposeEmail">From</label>' +
                        '<input id="emailComposeEmail" class="email-compose-input" type="email" maxlength="160" placeholder="your@email.com" required>' +
                    '</div>' +
                    '<div class="email-compose-row">' +
                        '<label class="email-compose-label" for="emailComposeMessage">Message</label>' +
                        '<textarea id="emailComposeMessage" class="email-compose-textarea" placeholder="Write your message to the admin..." required></textarea>' +
                    '</div>' +
                    '<div class="email-compose-row">' +
                        '<label class="email-compose-label" for="emailComposeLink">Links (optional)</label>' +
                        '<input id="emailComposeLink" class="email-compose-input" type="text" placeholder="https://... optionally paste link here">' +
                    '</div>' +
                    '<div class="email-compose-row">' +
                        '<label class="email-compose-label" for="emailComposeAttachment">Attachments (File/Image)</label>' +
                        '<input id="emailComposeAttachment" class="email-compose-input" type="file" multiple>' +
                    '</div>' +
                '</form>' +
                '<div id="emailComposeHint">Email will be sent directly from this website.</div>' +
                '<div id="emailComposeStatus" role="status" aria-live="polite"></div>' +
                '<div id="emailComposeFooter">' +
                    '<button id="emailComposeCancel" class="email-compose-btn cancel" type="button">Cancel</button>' +
                    '<button id="emailComposeSend" class="email-compose-btn send" type="submit" form="emailComposeForm">Send Email</button>' +
                '</div>' +
            '</div>';

        document.body.appendChild(overlay);

        var modal = {
            overlay: overlay,
            form: document.getElementById('emailComposeForm'),
            closeBtn: document.getElementById('emailComposeClose'),
            cancelBtn: document.getElementById('emailComposeCancel'),
            sendBtn: document.getElementById('emailComposeSend'),
            status: document.getElementById('emailComposeStatus'),
            to: document.getElementById('emailComposeTo'),
            subject: document.getElementById('emailComposeSubject'),
            name: document.getElementById('emailComposeName'),
            email: document.getElementById('emailComposeEmail'),
            message: document.getElementById('emailComposeMessage'),
            link: document.getElementById('emailComposeLink'),
            attachment: document.getElementById('emailComposeAttachment')
        };

        function setStatus(message, type) {
            modal.status.textContent = message || '';
            modal.status.className = type || '';
        }

        function setSendingState(isSending) {
            modal.sendBtn.disabled = !!isSending;
            modal.sendBtn.textContent = isSending ? 'Sending...' : 'Send Email';
        }

        modal.closeBtn.addEventListener('click', closeCompose);
        modal.cancelBtn.addEventListener('click', closeCompose);
        modal.overlay.addEventListener('click', function (e) {
            if (e.target === modal.overlay) closeCompose();
        });

        modal.form.addEventListener('submit', function (e) {
            e.preventDefault();

            var subject = String(modal.subject.value || '').trim();
            var message = String(modal.message.value || '').trim();
            var senderName = String(modal.name.value || '').trim();
            var senderEmail = String(modal.email.value || '').trim();

            if (!subject) {
                modal.subject.focus();
                return;
            }
            if (!message) {
                modal.message.focus();
                return;
            }
            if (!senderEmail) {
                setStatus('Please enter your email in the From field.', 'error');
                modal.email.focus();
                return;
            }
            if (!isValidEmail(senderEmail)) {
                setStatus('Please enter a valid email address in the From field.', 'error');
                modal.email.focus();
                return;
            }

            var payload;
            if (typeof FormData !== 'undefined') {
                payload = new FormData();
                payload.append('subject', subject);
                payload.append('message', message);
                payload.append('sender_name', senderName);
                payload.append('sender_email', senderEmail);
                payload.append('page_url', window.location.href);

                if (modal.link && modal.link.value) {
                    payload.append('links', modal.link.value.trim());
                }

                if (modal.attachment && modal.attachment.files && modal.attachment.files.length > 0) {
                    for (var i = 0; i < modal.attachment.files.length; i++) {
                        payload.append('attachments[]', modal.attachment.files[i]);
                    }
                }
            } else {
                payload = {
                    subject: subject,
                    message: message,
                    sender_name: senderName,
                    sender_email: senderEmail,
                    page_url: window.location.href
                };
                if (modal.link && modal.link.value) {
                    payload.links = modal.link.value.trim();
                }
            }

            setStatus('Sending your email...', 'info');
            setSendingState(true);

            sendEmailToServer(payload)
                .then(function (result) {
                    setStatus((result && result.message) || 'Email sent successfully.', 'success');
                    setTimeout(function () {
                        closeCompose();
                    }, 900);
                })
                .catch(function (err) {
                    var msg = err && err.message ? err.message : 'Unable to send your email right now.';
                    setStatus(msg, 'error');
                })
                .then(function () {
                    setSendingState(false);
                });
        });

        setStatus('', '');
        setSendingState(false);

        return modal;
    }

    function openCompose(triggerBtn) {
        var modal = ensureModal();
        var subject = (triggerBtn && triggerBtn.getAttribute('data-subject')) || 'Client Inquiry';
        var message = (triggerBtn && triggerBtn.getAttribute('data-message')) || '';

        modal.to.value = ADMIN_EMAIL;
        modal.subject.value = subject;
        modal.message.value = message;
        if (modal.link) modal.link.value = '';
        if (modal.attachment) modal.attachment.value = '';

        var statusEl = document.getElementById('emailComposeStatus');
        if (statusEl) {
            statusEl.textContent = '';
            statusEl.className = '';
        }

        modal.overlay.classList.add('open');
        modal.overlay.setAttribute('aria-hidden', 'false');
        setTimeout(function () {
            if (modal.email && !modal.email.value) {
                modal.email.focus();
            } else {
                modal.subject.focus();
                modal.subject.select();
            }
        }, 10);
    }

    function closeCompose() {
        var overlay = document.getElementById('emailComposeOverlay');
        if (!overlay) return;
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeCompose();
    });

    // Event delegation handles dynamically injected header buttons as well.
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.email-admin-btn');
        if (!btn) return;
        e.preventDefault();
        openCompose(btn);
    });
})();
