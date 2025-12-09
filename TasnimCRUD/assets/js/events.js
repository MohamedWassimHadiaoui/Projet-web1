// assets/js/events.js
(function () {
    function qs(selector) { return document.querySelector(selector); }
    function trim(v) { return (v || '').replace(/^\s+|\s+$/g,''); }

    var chatWidget = {
        initialized: false,
        history: null,
        input: null,
        sendBtn: null,
        clearBtn: null,
        status: null,
        escalateBtn: null,
    };

    function isDateTime(str) {
        // Accepts: YYYY-MM-DD or YYYY-MM-DD hh:mm
        var r = /^\d{4}-\d{2}-\d{2}(?:[ \t]\d{2}:\d{2})?$/;
        return r.test(str);
    }

    function showError(el, msg) {
        var existing = el.parentNode.querySelector('.field-error');
        if (existing) existing.parentNode.removeChild(existing);

        var span = document.createElement('span');
        span.className = 'field-error';
        span.style.color = 'red';
        span.style.display = 'block';
        span.style.marginTop = '0.25rem';
        span.textContent = msg;
        el.parentNode.appendChild(span);
    }

    function clearErrors(form) {
        var errors = form.querySelectorAll('.field-error');
        for (var i = 0; i < errors.length; i++) {
            errors[i].parentNode.removeChild(errors[i]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = qs('#eventForm');
        if (!form) return;

        form.addEventListener('submit', function (ev) {
            clearErrors(form);
            var title = trim(qs('#title').value);
            var datev = trim(qs('#date_event').value);
            var type = trim(qs('#type').value);
            var location = trim(qs('#location').value);
            var participants = trim(qs('#participants').value);
            var description = trim(qs('#description').value);

            var ok = true;
            if (!title || title.length < 3) { showError(qs('#title'), 'Le titre doit faire au moins 3 caractères'); ok = false; }
            if (!datev || !isDateTime(datev)) { showError(qs('#date_event'), 'Date invalide (format : YYYY-MM-DD ou YYYY-MM-DD HH:MM)'); ok = false; }
            if (!type) { showError(qs('#type'), 'Choisissez un type'); ok = false; }
            if (!location) { showError(qs('#location'), 'Ajoutez un lieu'); ok = false; }
            if (participants && isNaN(Number(participants))) { showError(qs('#participants'), 'Participants doit être un nombre'); ok = false; }
            if (!description || description.length < 10) { showError(qs('#description'), 'La description doit contenir au moins 10 caractères'); ok = false; }

            if (!ok) {
                ev.preventDefault();
                return false;
            }
            // else submit
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        initChatbotWidget();
        initCustomTabs();
    });

    // Tabs initializer to switch content for events, contenus and assistant
    function initCustomTabs() {
        var tabBtns = document.querySelectorAll('.tab-btn');
        if (!tabBtns || !tabBtns.length) return;
        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                // remove active from all
                tabBtns.forEach(function (b) { b.classList.remove('active'); });
                var tabs = document.querySelectorAll('.tab-content');
                tabs.forEach(function (t) { t.classList.remove('active'); });
                // activate clicked tab
                btn.classList.add('active');
                var tabName = btn.getAttribute('data-tab');
                var el = document.getElementById(tabName);
                if (el) el.classList.add('active');
                // If showing the assistant, focus the input
                if (tabName === 'assistant') {
                    setTimeout(function () {
                        var input = document.getElementById('chatInput');
                        if (input) { input.focus(); }
                    }, 250);
                }
            });
        });
    }

    function initChatbotWidget() {
        chatWidget.history = qs('#chatHistory');
        chatWidget.input = qs('#chatInput');
        chatWidget.sendBtn = qs('#chatSendBtn');
        chatWidget.clearBtn = qs('#chatClearBtn');
        chatWidget.status = qs('#chatStatus');
        chatWidget.escalateBtn = qs('#chatEscalateBtn');

        if (!chatWidget.history || !chatWidget.input || !chatWidget.sendBtn) {
            return;
        }

        chatWidget.initialized = true;
        chatWidget.clearBtn && chatWidget.clearBtn.addEventListener('click', resetChatHistory);
        chatWidget.sendBtn.addEventListener('click', handleChatSubmit);
        chatWidget.input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                handleChatSubmit();
            }
        });
        if (chatWidget.escalateBtn) {
            chatWidget.escalateBtn.addEventListener('click', function () {
                alert('Votre demande sera transmise à un médiateur humain.');
            });
        }
        var debugToggle = qs('#chatDebugToggle');
        var debugPanel = qs('#chatDebug');
        if (debugToggle && debugPanel) {
            debugToggle.addEventListener('click', function () {
                if (debugPanel.style.display === 'none' || debugPanel.style.display === '') {
                    debugPanel.style.display = 'block';
                    debugToggle.textContent = 'Cacher le debug';
                } else {
                    debugPanel.style.display = 'none';
                    debugToggle.textContent = 'Afficher le debug';
                }
            });
        }
        appendChatMessage('Assistant', 'Bonjour ! Comment puis-je vous aider dans vos démarches de médiation ?', 'bot');
    }

    function appendChatMessage(author, text, role) {
        if (!chatWidget.history) { return; }
        var bubble = document.createElement('div');
        bubble.className = 'chat-line chat-' + (role || 'bot');
        bubble.innerHTML = '<strong>' + author + '</strong><p>' + escapeHtml(text) + '</p>';
        chatWidget.history.appendChild(bubble);
        chatWidget.history.scrollTop = chatWidget.history.scrollHeight;
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function setChatStatus(message, isError) {
        if (!chatWidget.status) { return; }
        chatWidget.status.textContent = message || '';
        chatWidget.status.classList.toggle('error', !!isError);
    }

    function setChatDebug(payload) {
        var debugPanel = qs('#chatDebug');
        if (!debugPanel) return;
        if (!payload) {
            debugPanel.style.display = 'none';
            debugPanel.textContent = '';
            return;
        }
        var text = '';
        if (typeof payload === 'string') {
            text = payload;
        } else {
            try { text = JSON.stringify(payload, null, 2); } catch (e) { text = String(payload); }
        }
        debugPanel.style.display = 'block';
        debugPanel.textContent = text;
    }

    function setChatLoading(isLoading) {
        if (chatWidget.sendBtn) {
            chatWidget.sendBtn.disabled = !!isLoading;
        }
        if (isLoading) {
            setChatStatus('Assistant en cours de réponse...', false);
        } else {
            setChatStatus('');
        }
    }

    function resetChatHistory() {
        if (chatWidget.history) {
            chatWidget.history.innerHTML = '';
        }
        setChatStatus('');
        hideEscalateButton();
        appendChatMessage('Assistant', 'Conversation effacée. Posez une nouvelle question.', 'bot');
    }

    function showEscalateButton() {
        if (chatWidget.escalateBtn) {
            chatWidget.escalateBtn.style.display = 'inline-flex';
        }
    }

    function hideEscalateButton() {
        if (chatWidget.escalateBtn) {
            chatWidget.escalateBtn.style.display = 'none';
        }
    }

    function handleChatSubmit() {
        if (!chatWidget.initialized) { return; }
        var message = trim(chatWidget.input.value || '');
        if (!message) {
            setChatStatus('Merci de saisir un message avant envoi.', true);
            return;
        }

        appendChatMessage('Vous', message, 'user');
        chatWidget.input.value = '';
        setChatLoading(true);

        checkModeration(message)
            .then(function (result) {
                if (!result.allowed) {
                    setChatLoading(false);
                    setChatStatus('Message bloqué par la modération automatique.', true);
                    showEscalateButton();
                    return;
                }
                hideEscalateButton();
                return sendToChatbot(message, function (reply) {
                    appendChatMessage('Assistant', reply, 'bot');
                    setChatLoading(false);
                }, function (err) {
                    console.error('Chatbot error', err, err && err.serverData ? err.serverData : null);
                    setChatStatus(err.message || 'Le chatbot est indisponible.', true);
                    setChatLoading(false);
                });
            })
            .catch(function (error) {
                setChatLoading(false);
                setChatStatus(error.message || 'Erreur lors de la modération.', true);
            });
    }

    function collectEventSummaries(limit) {
        var summaries = [];
        var cards = document.querySelectorAll('.event-card');
        if (!cards || !cards.length) { return summaries; }
        var max = typeof limit === 'number' ? limit : 5;
        for (var i = 0; i < cards.length && i < max; i++) {
            var card = cards[i];
            var titleEl = card.querySelector('.card-title');
            var blocks = card.querySelectorAll('.event-info');
            var dateText = '';
            if (blocks.length) {
                var firstSpans = blocks[0].querySelectorAll('span');
                if (firstSpans.length > 1) {
                    dateText = firstSpans[1].textContent || '';
                }
            }
            var locationText = '';
            if (blocks.length > 1) {
                var locationSpans = blocks[1].querySelectorAll('span');
                if (locationSpans.length > 1) {
                    locationText = locationSpans[1].textContent || '';
                }
            }
            summaries.push({
                title: trim(titleEl ? titleEl.textContent : ''),
                date: trim(dateText),
                location: trim(locationText),
            });
        }
        return summaries;
    }

    function fetchJson(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(body || {})
            }).then(function (response) {
                return response.json().catch(function () {
                    // JSON parse failed - fallback to text to surface server output in errors
                    return response.text().then(function (raw) {
                        return { _raw_text: raw || '' };
                    }).catch(function () { return { _raw_text: '' }; });
                }).then(function (data) {
                if (!response.ok) {
                        var message = (data && data.error) ? data.error : ('Requête échouée (' + response.status + ')');
                        // if raw text is present and JSON parse failed, append snippet for debugging
                        if (data && data._raw_text && data._raw_text.length) {
                            message += '\n- raw: ' + (data._raw_text.slice(0, 500));
                        }
                    throw new Error(message);
                }
                return data;
            });
        });
    }

    function sendToChatbot(messageOrMessages, onReply, onError, model) {
        var payload = {};
        if (Array.isArray(messageOrMessages)) {
            payload.messages = messageOrMessages;
        } else {
            payload.message = messageOrMessages;
        }
        payload.context = {
                page: 'events',
                timestamp: new Date().toISOString(),
                user_id: window.currentUserId || null,
                interests: ['médiation', 'paix'],
                events: collectEventSummaries(5)
        }
        if (model) {
            payload.model = model;
        } else if (window.COHERE_MODEL) {
            payload.model = window.COHERE_MODEL;
        }

        return fetchJson('/TasnimCRUD/api/chatbot.php', payload)
            .then(function (data) {
                // pick reply using possible fields first; allow success even if data.ok is missing
                var replyText = null;
                // If the response could be a fallback object with raw text, try to parse it
                if (data && data._raw_text) {
                    try {
                        var maybe = JSON.parse(data._raw_text);
                        if (maybe && typeof maybe === 'object') {
                            data = maybe; // use parsed object for the rest
                        }
                    } catch (e) {
                        // not JSON, stick with _raw_text in fallback
                    }
                }
                if (data && data.reply && String(data.reply).trim() !== '') {
                    replyText = String(data.reply).trim();
                } else if (data && data.text && String(data.text).trim() !== '') {
                    replyText = String(data.text).trim();
                } else if (data && data.message && data.message.content && Array.isArray(data.message.content) && data.message.content.length) {
                    replyText = data.message.content.map(function (c) { return c && (c.text || c) ? (c.text || c) : ''; }).join(' ').trim();
                } else if (data && data.cohere && data.cohere.body) {
                    // Sometimes the data contains the full Cohere body under data.cohere.body
                    try {
                        var cb = data.cohere.body;
                        if (cb.message && cb.message.content && Array.isArray(cb.message.content) && cb.message.content.length) {
                            replyText = cb.message.content.map(function (c) { return c && (c.text || c) ? (c.text || c) : ''; }).join(' ').trim();
                        } else if (cb.text && String(cb.text).trim() !== '') {
                            replyText = String(cb.text).trim();
                        } else if (cb.generations && Array.isArray(cb.generations) && cb.generations.length) {
                            replyText = cb.generations.map(function (g) { return g && (g.text || '') ? (g.text || '') : ''; }).join(' ').trim();
                        }
                    } catch (e) {
                        // ignore
                    }
                }
                // If we don't have an explicit reply but the server explicitly returned ok:false then it's an error
                if (!replyText && (!data || !data.ok)) {
                    var message = (data && (data.error || data.message)) ? (data.error || data.message) : 'Réponse invalide du serveur';
                    if (data && data._raw_text) {
                        message += '\nRaw: ' + data._raw_text.slice(0, 500);
                    }
                    console.warn('Chatbot: serveur renvoyé', data);
                    throw new Error(message);
                }
                if (!replyText || replyText === '') {
                    console.warn('Chatbot: aucune réponse reçue du serveur', data);
                    var rawPreview = JSON.stringify(data).slice(0, 500);
                    // Try to fallback to a choices array if available (C++ style response)
                    var fallbackReply = null;
                    if (data && data.choices && Array.isArray(data.choices) && data.choices.length) {
                        try {
                            var first = data.choices[0];
                            if (first && first.message && Array.isArray(first.message.content) && first.message.content.length) {
                                fallbackReply = String(first.message.content.map(function (c) { return c.text || ''; }).join(' ')).trim();
                            }
                        } catch (e) {
                            // ignore
                        }
                    }
                    if (fallbackReply) {
                        console.info('Chatbot: using fallback reply from choices array');
                        if (typeof onReply === 'function') {
                            onReply(fallbackReply);
                        }
                        return data;
                    }
                        var err = new Error('Le chatbot n\'a pas fourni de texte. Voir console pour le détail.');
                        err.serverData = data;
                        // show raw snippet in UI debug panel
                        if (data && data._raw_text) setChatDebug(data._raw_text);
                        else setChatDebug(data);
                        throw err;
                }
                if (typeof onReply === 'function' && replyText) {
                    // Clear debug panel when we receive a meaningful reply
                    setChatDebug(null);
                    onReply(replyText);
                }
                return data;
            })
                .catch(function (error) {
                if (typeof onError === 'function') {
                    onError(error);
                } else {
                    console.error(error);
                }
                // Show raw server snippet if present in `_raw_text` or serverData
                try {
                    if (error && error.serverData && error.serverData._raw_text) {
                        setChatStatus('Réponse invalide du serveur (raw snippet). Voir ci-dessous.', true);
                        console.warn('Server raw text:', error.serverData._raw_text);
                        setChatDebug(error.serverData._raw_text);
                    } else if (error && error.serverData && error.serverData.raw) {
                        setChatStatus('Réponse invalide du serveur (raw snippet). Voir ci-dessous.', true);
                        console.warn('Server raw:', error.serverData.raw);
                        setChatDebug(error.serverData.raw);
                    }
                } catch (e) {
                    // ignore
                }
                throw error;
            });
    }

    function checkModeration(text) {
        return fetchJson('/TasnimCRUD/api/moderation.php', { text: text })
            .then(function (data) {
                if (data.flag) {
                    alert('Votre message semble enfreindre les règles de la communauté.');
                }
                return { allowed: !data.flag, scores: data.scores || {} };
            });
    }

    window.sendToChatbot = sendToChatbot;
    window.checkModeration = checkModeration;
})();