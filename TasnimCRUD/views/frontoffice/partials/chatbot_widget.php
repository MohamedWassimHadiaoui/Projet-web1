<style>
    .chatbot-card {
        padding: 2rem;
        margin-top: 1rem;
    }
    .chat-history {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        height: 280px;
        overflow-y: auto;
        background: #f9fafb;
        margin-bottom: 1rem;
    }
    .chat-line {
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
    }
    .chat-user {
        background: #dbeafe;
        text-align: right;
    }
    .chat-bot {
        background: #fef3c7;
    }
    .chat-line p {
        margin: 0.375rem 0 0;
    }
    .chat-controls {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .chat-controls input {
        flex: 1;
        min-width: 180px;
    }
    .chat-status {
        min-height: 1.5rem;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    .chat-status.error {
        color: #b91c1c;
    }
</style>
<section class="section" id="chatbotSection">
    <div class="container">
        <div class="card chatbot-card">
            <div class="section-title" style="text-align:left;">
                <h2>Assistant médiation instantané</h2>
                <p>Discutez en direct avec l'IA Cohere pour obtenir des conseils pratiques.</p>
            </div>
            <div id="chatHistory" class="chat-history" aria-live="polite"></div>
            <div class="chat-controls">
                <input type="text" id="chatInput" class="form-control" placeholder="Posez votre question sur la médiation...">
                <button type="button" id="chatSendBtn" class="btn btn-primary">Envoyer</button>
                <button type="button" id="chatClearBtn" class="btn btn-outline">Effacer</button>
            </div>
            <div id="chatStatus" class="chat-status" role="status"></div>
            <button type="button" id="chatEscalateBtn" class="btn btn-outline" style="display:none; margin-top:0.5rem;">Demander révision humaine</button>
            <div style="margin-top:0.5rem; display:flex; gap:0.5rem; align-items:center;">
                <button id="chatDebugToggle" type="button" class="btn btn-outline">Afficher le debug</button>
                <div id="chatDebug" style="display:none; width:100%; max-height:200px; overflow:auto; font-family: monospace; font-size:12px; background:#f3f4f6; border: 1px dashed #e5e7eb; padding:8px; border-radius:4px;"></div>
            </div>
        </div>
    </div>
</section>
