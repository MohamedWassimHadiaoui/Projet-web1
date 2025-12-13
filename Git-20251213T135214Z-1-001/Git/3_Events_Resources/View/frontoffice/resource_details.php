<?php
session_start();
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/contenuController.php";

$cc = new ContenuController();
$id = (int)($_GET['id'] ?? 0);
$resource = $id ? $cc->getContenuById($id) : null;

if (!$resource) {
    header("Location: " . $frontoffice . "resources.php");
    exit;
}

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($resource['title'] ?? 'Resource') ?> - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .resource-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.5rem;
            margin-top: 1.5rem;
        }
        .resource-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        .resource-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .resource-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .resource-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .resource-body {
            color: var(--text-primary);
            line-height: 1.8;
            font-size: 1.05rem;
        }
        .resource-body p {
            margin-bottom: 1.25rem;
        }
        .resource-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        .like-btn {
            background: linear-gradient(135deg, #ec4899, #db2777);
            border: none;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        .like-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(236, 72, 153, 0.3);
        }
        .like-count {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ec4899;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .speech-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        .speech-btn:hover {
            transform: translateY(-2px);
        }
        .speech-btn.speaking {
            background: #ef4444;
        }
        .tags-section {
            margin-top: 1.5rem;
        }
        .tags-section .badge {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .resource-body h3 { font-size: 1.3rem; font-weight: 600; margin: 1.5rem 0 0.75rem; color: var(--text-primary); }
        .resource-body ul, .resource-body ol { margin: 1rem 0; padding-left: 1.5rem; }
        .resource-body li { margin-bottom: 0.5rem; }
        .resource-body strong { font-weight: 600; }
        .translate-section { margin-top: 1.5rem; padding: 1rem; background: rgba(99,102,241,0.1); border-radius: 12px; }
        .translate-btns { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; }
        .translate-btn { padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-card); color: var(--text-primary); cursor: pointer; font-size: 0.9rem; transition: all 0.2s; }
        .translate-btn:hover, .translate-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }
        #translatedContent { padding: 1rem; background: var(--bg-card); border-radius: 8px; display: none; }
        #translatedContent.show { display: block; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <a class="btn btn-secondary" href="<?= $frontoffice ?>resources.php">← Back to Resources</a>

            <?php if ($success): ?>
            <div class="alert alert-success" style="margin-top:1rem"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="resource-content">
                <div class="resource-header">
                    <h1>📖 <?= htmlspecialchars($resource['title']) ?></h1>
                    <div class="resource-meta">
                        <span>✍️ By <?= htmlspecialchars($resource['author'] ?? 'PeaceConnect Team') ?></span>
                        <span>📅 <?= date('F j, Y', strtotime($resource['created_at'])) ?></span>
                        <span class="like-count">❤️ <?= (int)($resource['likes'] ?? 0) ?> likes</span>
                    </div>
                </div>

                <div class="resource-body" id="originalContent">
                    <?php
                    $body = $resource['body'] ?? '';
                    $allowedTags = '<h1><h2><h3><h4><h5><h6><p><br><ul><ol><li><strong><em><b><i><a><blockquote>';
                    echo strip_tags($body, $allowedTags);
                    ?>
                </div>

                <div class="translate-section">
                    <strong>🌐 Translate to:</strong>
                    <div class="translate-btns">
                        <button type="button" class="translate-btn active" data-lang="original">📄 Original</button>
                        <button type="button" class="translate-btn" data-lang="en">🇬🇧 English</button>
                        <button type="button" class="translate-btn" data-lang="fr">🇫🇷 Français</button>
                        <button type="button" class="translate-btn" data-lang="ar">🇸🇦 العربية</button>
                    </div>
                    <div id="translatedContent"></div>
                </div>

                <?php if (!empty($resource['tags'])): ?>
                <div class="tags-section">
                    <strong>Tags:</strong>
                    <?php foreach (explode(',', $resource['tags']) as $tag): $tag = trim($tag); if ($tag === '') continue; ?>
                        <span class="badge badge-low"><?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="resource-actions">
                    <form action="<?= $controller ?>contenuController.php" method="POST" style="display:inline">
                        <input type="hidden" name="action" value="like">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="return_to" value="<?= $frontoffice ?>resource_details.php?id=<?= $id ?>">
                        <button type="submit" class="like-btn">❤️ Like this Resource</button>
                    </form>

                    <button type="button" class="speech-btn" id="readBtn" 
                        data-titre="<?= htmlspecialchars($resource['title'], ENT_QUOTES, 'UTF-8') ?>"
                        data-contenu="<?= htmlspecialchars(strip_tags($resource['body'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        🔊 Read Aloud
                    </button>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script>
    document.getElementById('readBtn')?.addEventListener('click', function() {
        const btn = this;
        const title = btn.dataset.titre;
        const content = btn.dataset.contenu;
        
        if ('speechSynthesis' in window) {
            if (speechSynthesis.speaking) {
                speechSynthesis.cancel();
                btn.classList.remove('speaking');
                btn.innerHTML = '🔊 Read Aloud';
                return;
            }
            
            const text = title + '. ' + content;
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            utterance.rate = 0.9;
            
            utterance.onstart = () => {
                btn.classList.add('speaking');
                btn.innerHTML = '⏹️ Stop Reading';
            };
            
            utterance.onend = () => {
                btn.classList.remove('speaking');
                btn.innerHTML = '🔊 Read Aloud';
            };
            
            utterance.onerror = () => {
                btn.classList.remove('speaking');
                btn.innerHTML = '🔊 Read Aloud';
            };
            
            speechSynthesis.speak(utterance);
        } else {
            alert('Speech synthesis is not supported in your browser.');
        }
    });

    // Translation feature with auto-detect
    const translateBtns = document.querySelectorAll('.translate-btn');
    const originalContent = document.getElementById('originalContent');
    const translatedBox = document.getElementById('translatedContent');
    const originalText = originalContent.innerText;

    function detectLanguage(text) {
        const arabicPattern = /[\u0600-\u06FF]/;
        const frenchPattern = /[àâäéèêëïîôùûüÿçœæ]/i;
        const frenchWords = /\b(le|la|les|de|du|des|et|est|sont|pour|avec|dans|sur|une|que|qui|ce|cette|vous|nous|ils|elles|avoir|être|faire|aller|voir|pouvoir|vouloir)\b/i;
        
        if (arabicPattern.test(text)) return 'ar';
        if (frenchPattern.test(text) || frenchWords.test(text)) return 'fr';
        return 'en';
    }

    const detectedLang = detectLanguage(originalText);
    const langNames = { en: 'English', fr: 'French', ar: 'Arabic' };
    console.log('Detected language:', langNames[detectedLang]);

    async function translateText(text, fromLang, toLang) {
        if (fromLang === toLang) return text;
        
        const chunks = [];
        const maxLen = 500;
        for (let i = 0; i < text.length; i += maxLen) {
            chunks.push(text.substring(i, i + maxLen));
        }
        
        const translations = [];
        for (const chunk of chunks.slice(0, 3)) {
            try {
                const response = await fetch('https://api.mymemory.translated.net/get?q=' + 
                    encodeURIComponent(chunk) + '&langpair=' + fromLang + '|' + toLang);
                const data = await response.json();
                if (data.responseStatus === 200 && data.responseData.translatedText) {
                    translations.push(data.responseData.translatedText);
                } else {
                    translations.push(chunk);
                }
            } catch (e) {
                translations.push(chunk);
            }
        }
        return translations.join(' ');
    }

    translateBtns.forEach(btn => {
        btn.addEventListener('click', async function() {
            const targetLang = this.dataset.lang;
            
            translateBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (targetLang === 'original') {
                originalContent.style.display = 'block';
                translatedBox.classList.remove('show');
                return;
            }

            translatedBox.innerHTML = '<p style="color:var(--text-muted)">🔄 Detecting language and translating...</p>';
            translatedBox.classList.add('show');
            originalContent.style.display = 'none';

            try {
                const translated = await translateText(originalText, detectedLang, targetLang);
                
                if (targetLang === 'ar') {
                    translatedBox.innerHTML = '<div dir="rtl" style="text-align:right;line-height:1.8">' + translated + '</div>';
                } else {
                    translatedBox.innerHTML = '<div style="line-height:1.8">' + translated + '</div>';
                }
                
                translatedBox.innerHTML += '<p style="margin-top:1rem;font-size:0.85rem;color:var(--text-muted)">Translated from ' + langNames[detectedLang] + ' to ' + langNames[targetLang] + '</p>';
            } catch (err) {
                translatedBox.innerHTML = '<p style="color:var(--danger)">Translation service unavailable. Please try again.</p>';
            }
        });
    });
    </script>
</body>
</html>

