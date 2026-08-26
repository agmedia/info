import './bootstrap';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import Chart from 'chart.js/auto';
import { initSpaceBanner } from './front/space-banner';

let aceLoaderPromise = null;
let aceInlineFailureNotified = false;

const loadAce = async () => {
    if (aceLoaderPromise) {
        return aceLoaderPromise;
    }

    aceLoaderPromise = (async () => {
        const aceModule = await import('ace-builds/src-noconflict/ace');
        const resolvedAce = aceModule.default ?? aceModule;
        const ace = resolvedAce?.default ?? resolvedAce;

        if (typeof window !== 'undefined' && ace) {
            window.ace = ace;
        }

        await import('ace-builds/src-noconflict/ext-language_tools');
        await import('ace-builds/src-noconflict/mode-html');
        await import('ace-builds/src-noconflict/theme-tomorrow_night');

        const readyAce = (typeof window !== 'undefined' ? window.ace : null) || ace;
        if (!readyAce || typeof readyAce.edit !== 'function') {
            throw new Error('Ace core failed to initialize.');
        }

        return readyAce;
    })()
        .catch((error) => {
            aceLoaderPromise = null;
            throw error;
        });

    return aceLoaderPromise;
};

const initAceLauncher = () => {
    const overlay = document.getElementById('admin-ace-overlay');
    const editorRoot = document.getElementById('admin-ace-editor');
    const titleNode = document.getElementById('admin-ace-title');
    const closeButton = document.getElementById('admin-ace-close');
    const cancelButton = document.getElementById('admin-ace-cancel');
    const applyButton = document.getElementById('admin-ace-apply');

    if (!overlay || !editorRoot || !closeButton || !cancelButton || !applyButton) {
        return;
    }

    if (overlay.dataset.aceLauncherReady === '1') {
        return;
    }
    overlay.dataset.aceLauncherReady = '1';

    let editor = null;
    let targetTextarea = null;
    let openNonce = 0;

    const ensureEditor = async () => {
        if (editor) {
            return editor;
        }

        const ace = await loadAce();
        editor = ace.edit(editorRoot);
        editor.session.setMode('ace/mode/html');
        editor.setTheme('ace/theme/tomorrow_night');
        editor.setOptions({
            fontSize: '13px',
            showPrintMargin: false,
            useSoftTabs: true,
            tabSize: 2,
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
        });
        editor.session.setUseWorker(false);

        return editor;
    };

    const close = () => {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        targetTextarea = null;
    };

    const open = async (textarea, label) => {
        const requestNonce = ++openNonce;
        targetTextarea = textarea;
        titleNode.textContent = label || 'HTML Editor';
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');

        try {
            const loadedEditor = await ensureEditor();
            if (requestNonce !== openNonce || !overlay.classList.contains('is-open')) {
                return;
            }

            loadedEditor.setValue(textarea.value || '', -1);
            setTimeout(() => loadedEditor.focus(), 0);
        } catch (error) {
            console.error('Failed to load Ace editor', error);
            close();
            window.dispatchEvent(new CustomEvent('admin:notify', {
                detail: { type: 'danger', message: 'Ace editor failed to load.' },
            }));
        }
    };

    const apply = () => {
        if (!targetTextarea || !editor) {
            close();
            return;
        }

        const value = editor.getValue();
        if (targetTextarea.value !== value) {
            targetTextarea.value = value;
            targetTextarea.dispatchEvent(new Event('input', { bubbles: true }));
            targetTextarea.dispatchEvent(new Event('change', { bubbles: true }));
        }

        close();
    };

    const bindLaunchButtons = () => {
        const buttons = document.querySelectorAll('[data-ace-open][data-ace-target]');
        buttons.forEach((button) => {
            if (button.dataset.aceBound === '1') {
                return;
            }
            button.dataset.aceBound = '1';

            button.addEventListener('click', async () => {
                const targetId = button.getAttribute('data-ace-target');
                if (!targetId) return;

                const textarea = document.getElementById(targetId);
                if (!(textarea instanceof HTMLTextAreaElement)) {
                    return;
                }

                await open(textarea, button.getAttribute('data-ace-label') || 'HTML Editor');
            });
        });
    };

    closeButton.addEventListener('click', close);
    cancelButton.addEventListener('click', close);
    applyButton.addEventListener('click', apply);

    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) {
            close();
        }
    });

    window.addEventListener('keydown', (event) => {
        if (!overlay.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            close();
        }

        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 's') {
            event.preventDefault();
            apply();
        }
    });

    bindLaunchButtons();

    const observer = new MutationObserver(() => {
        bindLaunchButtons();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
};

const initTomSelect = () => {
    if (!document.body) {
        return;
    }

    const buildTypeRender = () => ({
        option(data, escape) {
            const value = escape(String(data.value ?? ''));
            const label = escape(String(data.text ?? data.label ?? data.value ?? ''));
            return `<div class="tom-type-option"><span class="tom-type-chip tom-type-chip--${value}"></span><span>${label}</span></div>`;
        },
        item(data, escape) {
            const value = escape(String(data.value ?? ''));
            const label = escape(String(data.text ?? data.label ?? data.value ?? ''));
            return `<div class="tom-type-option"><span class="tom-type-chip tom-type-chip--${value}"></span><span>${label}</span></div>`;
        },
    });

    const bindElement = (element) => {
        if (!(element instanceof HTMLSelectElement)) {
            return;
        }
        const tom = element.tomselect;
        if (tom) {
            const wrapperConnected = Boolean(tom.wrapper?.isConnected);
            const controlConnected = Boolean(tom.control?.isConnected);
            if (wrapperConnected && controlConnected) {
                return;
            }

            try {
                tom.destroy();
            } catch (_error) {
                // Ignore stale instance destroy errors.
            }

            delete element.tomselect;
            delete element.dataset.tomSelectBound;
        }

        if (element.dataset.tomSelectBound === '1' && !element.tomselect) {
            delete element.dataset.tomSelectBound;
        }

        if (element.dataset.tomSelectBound === '1') {
            return;
        }

        const noSearch = element.dataset.tomNoSearch === '1';
        const allowCreate = element.dataset.tomCreate === '1';
        const visual = element.dataset.tomVisual || '';
        const placeholder = element.getAttribute('placeholder') || element.dataset.tomPlaceholder || '';
        const maxItemsRaw = element.dataset.tomMaxItems;
        const maxItems = maxItemsRaw ? Number.parseInt(maxItemsRaw, 10) : (element.multiple ? null : 1);

        const plugins = [];
        if (!noSearch) {
            plugins.push('dropdown_input');
        }
        if (element.multiple) {
            plugins.push('remove_button');
        }

        const config = {
            allowEmptyOption: true,
            maxItems: Number.isNaN(maxItems) ? (element.multiple ? null : 1) : maxItems,
            create: allowCreate ? (input) => {
                const value = String(input ?? '').trim();
                return value === '' ? false : { value, text: value };
            } : false,
            plugins,
            controlInput: noSearch ? null : undefined,
            searchField: noSearch ? [] : ['text'],
            sortField: [{ field: 'text', direction: 'asc' }],
            placeholder,
            onChange() {
                element.dispatchEvent(new Event('change', { bubbles: true }));
            },
        };

        if (visual === 'block-type') {
            config.render = buildTypeRender();
        }

        element.dataset.tomSelectBound = '1';
        // eslint-disable-next-line no-new
        new TomSelect(element, config);
    };

    const selectSelector = 'select[data-tom-select], select.admin-select:not([multiple])';

    const bindAll = (root) => {
        if (!root) {
            return;
        }
        if (root instanceof HTMLSelectElement && root.matches(selectSelector)) {
            bindElement(root);
        }
        root.querySelectorAll?.(selectSelector).forEach(bindElement);
    };

    bindAll(document);

    if (!window.__tomSelectObserver) {
        window.__tomSelectObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.target instanceof HTMLElement || mutation.target instanceof HTMLSelectElement) {
                    bindAll(mutation.target);
                }

                mutation.addedNodes.forEach((node) => {
                    if (node instanceof HTMLElement || node instanceof HTMLSelectElement) {
                        bindAll(node);
                    }
                });

                if (mutation.removedNodes.length > 0 && mutation.target instanceof HTMLElement) {
                    bindAll(mutation.target);
                }
            });
        });
    }

    if (window.__tomSelectObserverBody !== document.body) {
        window.__tomSelectObserver.disconnect();
        window.__tomSelectObserver.observe(document.body, {
            childList: true,
            subtree: true,
        });
        window.__tomSelectObserverBody = document.body;
    }
};

const initQuillEditors = () => {
    if (!document.body) {
        return;
    }

    const selector = 'textarea[data-quill-editor]';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const normalizeHtml = (html) => {
        const value = String(html ?? '').trim();
        return ['<p><br></p>', '<p></p>'].includes(value) ? '' : value;
    };

    const parseYouTubeTimestamp = (rawValue) => {
        const value = String(rawValue ?? '').trim().toLowerCase();
        if (value === '') {
            return 0;
        }

        if (/^\d+$/.test(value)) {
            return Number.parseInt(value, 10);
        }

        let totalSeconds = 0;
        let matchedLength = 0;
        const unitPattern = /(\d+)(h|m|s)/g;
        let match = unitPattern.exec(value);

        while (match) {
            const amount = Number.parseInt(match[1] || '0', 10);
            if (match[2] === 'h') {
                totalSeconds += amount * 3600;
            } else if (match[2] === 'm') {
                totalSeconds += amount * 60;
            } else {
                totalSeconds += amount;
            }

            matchedLength += match[0].length;
            match = unitPattern.exec(value);
        }

        return matchedLength === value.length ? totalSeconds : 0;
    };

    const readYouTubeStartSeconds = (url) => {
        const directParams = [
            url.searchParams.get('start'),
            url.searchParams.get('t'),
        ];

        for (const candidate of directParams) {
            const seconds = parseYouTubeTimestamp(candidate);
            if (seconds > 0) {
                return seconds;
            }
        }

        const hash = String(url.hash || '').replace(/^#/, '').trim();
        if (hash === '') {
            return 0;
        }

        if (hash.includes('=')) {
            const hashParams = new URLSearchParams(hash);
            const seconds = parseYouTubeTimestamp(hashParams.get('t') || hashParams.get('start'));
            return seconds > 0 ? seconds : 0;
        }

        return parseYouTubeTimestamp(hash);
    };

    const resolveYouTubeEmbedUrl = (rawValue) => {
        const input = String(rawValue ?? '').trim();
        if (input === '') {
            return '';
        }

        if (/^[a-zA-Z0-9_-]{11}$/.test(input)) {
            return `https://www.youtube.com/embed/${input}`;
        }

        const normalizedInput = /^[a-z][a-z\d+.-]*:\/\//i.test(input) ? input : `https://${input.replace(/^\/+/, '')}`;
        let parsedUrl = null;

        try {
            parsedUrl = new URL(normalizedInput);
        } catch (error) {
            return '';
        }

        const hostname = parsedUrl.hostname
            .replace(/^www\./i, '')
            .replace(/^m\./i, '')
            .toLowerCase();

        let videoId = '';
        if (hostname === 'youtu.be') {
            videoId = parsedUrl.pathname.split('/').filter(Boolean)[0] || '';
        } else if (hostname === 'youtube.com' || hostname === 'youtube-nocookie.com') {
            const segments = parsedUrl.pathname.split('/').filter(Boolean);

            if (segments[0] === 'watch') {
                videoId = parsedUrl.searchParams.get('v') || '';
            } else if (['embed', 'shorts', 'live'].includes(segments[0] || '')) {
                videoId = segments[1] || '';
            }
        }

        videoId = videoId.replace(/[^a-zA-Z0-9_-]/g, '');
        if (!/^[a-zA-Z0-9_-]{11}$/.test(videoId)) {
            return '';
        }

        const embedUrl = new URL(`/embed/${videoId}`, 'https://www.youtube.com');
        const startSeconds = readYouTubeStartSeconds(parsedUrl);
        if (startSeconds > 0) {
            embedUrl.searchParams.set('start', String(startSeconds));
        }

        return embedUrl.toString();
    };

    const stripInlineStyles = (html) => {
        const source = String(html ?? '').trim();
        if (source === '') {
            return '';
        }

        const parser = new DOMParser();
        const doc = parser.parseFromString(source, 'text/html');
        doc.body.querySelectorAll('[style]').forEach((node) => node.removeAttribute('style'));

        return normalizeHtml(doc.body.innerHTML || '');
    };

    const readTextareaHtml = (textarea) => {
        if (!(textarea instanceof HTMLTextAreaElement)) {
            return '';
        }

        const fromValue = stripInlineStyles(normalizeHtml(textarea.value));
        if (fromValue !== '') {
            return fromValue;
        }

        // Livewire can occasionally hydrate <textarea> content without dispatching input/change.
        return stripInlineStyles(normalizeHtml(textarea.textContent ?? ''));
    };

    const bindElement = (textarea) => {
        if (!(textarea instanceof HTMLTextAreaElement)) {
            return;
        }
        const existingQuillWrapper = textarea.nextElementSibling instanceof HTMLElement
            && textarea.nextElementSibling.classList.contains('admin-quill')
            ? textarea.nextElementSibling
            : null;

        // Livewire can remove injected Quill wrapper during morphs.
        // If that happened, clear the bound flag so editor can be mounted again.
        if (textarea.dataset.quillBound === '1' && !existingQuillWrapper) {
            delete textarea.dataset.quillBound;
            textarea.style.display = '';
            textarea.removeAttribute('aria-hidden');
            textarea.tabIndex = 0;
        }

        // If this field was previously mounted with Ace, tear it down and restore textarea.
        const staleAce = textarea.nextElementSibling;
        if (staleAce instanceof HTMLElement && staleAce.classList.contains('admin-ace-inline')) {
            staleAce.remove();
            textarea.style.display = '';
            textarea.removeAttribute('aria-hidden');
            textarea.tabIndex = 0;
            delete textarea.dataset.aceInlineBound;
        }
        if (textarea.dataset.quillBound === '1') {
            return;
        }
        textarea.dataset.quillBound = '1';
        const imageUploadUrl = String(textarea.dataset.quillImageUploadUrl || '').trim();
        const editorProfile = String(textarea.dataset.quillProfile || '').trim();
        const toolbarControls = editorProfile === 'service-text'
            ? [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'link'],
                ['clean'],
            ]
            : [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                imageUploadUrl !== '' ? ['blockquote', 'link', 'video', 'image'] : ['blockquote', 'link', 'video'],
                ['clean'],
            ];

        const wrapper = document.createElement('div');
        wrapper.className = 'admin-quill';

        const editorRoot = document.createElement('div');
        editorRoot.className = 'admin-quill-editor';
        wrapper.appendChild(editorRoot);

        textarea.insertAdjacentElement('afterend', wrapper);
        textarea.style.display = 'none';
        textarea.setAttribute('aria-hidden', 'true');
        textarea.tabIndex = -1;

        let quill = null;
        try {
            quill = new Quill(editorRoot, {
                theme: 'snow',
                modules: {
                    toolbar: toolbarControls,
                },
                placeholder: textarea.getAttribute('placeholder') || '',
            });
        } catch (error) {
            console.error('Failed to initialize Quill editor', error);
            wrapper.remove();
            textarea.style.display = '';
            textarea.removeAttribute('aria-hidden');
            textarea.tabIndex = 0;
            window.dispatchEvent(new CustomEvent('admin:notify', {
                detail: { type: 'danger', message: 'WYSIWYG editor failed to load.' },
            }));
            return;
        }

        // Paste sanitization: remove inline style attributes from incoming HTML.
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            if (node instanceof HTMLElement && node.hasAttribute('style')) {
                node.removeAttribute('style');
            }
            return delta;
        });

        if (!Array.isArray(window.__adminQuillEditors)) {
            window.__adminQuillEditors = [];
        }
        window.__adminQuillEditors.push(quill);
        window.__activeAdminQuill = quill;
        quill.__lastRange = null;
        quill.__selectedImage = null;
        let preserveSelectedImage = false;

        const clearSelectedImage = () => {
            if (quill.__selectedImage instanceof HTMLImageElement) {
                quill.__selectedImage.classList.remove('is-selected');
            }

            quill.__selectedImage = null;
        };

        const selectImageNode = (node) => {
            if (!(node instanceof HTMLImageElement) || !quill.root.contains(node)) {
                clearSelectedImage();
                return false;
            }

            if (quill.__selectedImage instanceof HTMLImageElement && quill.__selectedImage !== node) {
                quill.__selectedImage.classList.remove('is-selected');
            }

            quill.__selectedImage = node;
            quill.__selectedImage.classList.add('is-selected');
            return true;
        };

        quill.on('selection-change', (range) => {
            if (range) {
                window.__activeAdminQuill = quill;
                quill.__lastRange = range;
                const [leaf] = quill.getLeaf(range.index);
                if (!(leaf?.domNode instanceof HTMLImageElement && range.length === 1)) {
                    clearSelectedImage();
                }
                return;
            }

            if (preserveSelectedImage) {
                return;
            }

            clearSelectedImage();
        });

        editorRoot.addEventListener('click', (event) => {
            window.__activeAdminQuill = quill;

            const target = event.target;
            if (!(target instanceof HTMLImageElement) || !selectImageNode(target)) {
                clearSelectedImage();
                return;
            }

            const blot = Quill.find(target);
            if (!blot) {
                return;
            }

            const index = quill.getIndex(blot);
            quill.setSelection(index, 1, 'silent');
            quill.__lastRange = { index, length: 1 };
        });

        const rows = Number.parseInt(textarea.getAttribute('rows') || '8', 10);
        const minHeight = Number.isNaN(rows) ? 220 : Math.max(180, rows * 26);
        const editorNode = editorRoot.querySelector('.ql-editor');
        if (editorNode instanceof HTMLElement) {
            editorNode.style.minHeight = `${minHeight}px`;
        }

        const initial = readTextareaHtml(textarea);
        if (initial) {
            quill.clipboard.dangerouslyPasteHTML(initial);
        } else {
            quill.setText('');
        }

        let syncingFromQuill = false;
        let syncingFromTextarea = false;
        const readEditorHtml = () => {
            if (editorProfile === 'service-text' && typeof quill.getSemanticHTML === 'function') {
                return quill.getSemanticHTML();
            }

            return quill.root.innerHTML;
        };
        const notify = (type, message) => {
            window.dispatchEvent(new CustomEvent('admin:notify', {
                detail: { type, message },
            }));
        };

        const syncTextareaFromQuill = () => {
            if (syncingFromTextarea) {
                return;
            }
            const html = stripInlineStyles(normalizeHtml(readEditorHtml()));
            if (normalizeHtml(textarea.value) === html) {
                return;
            }
            syncingFromQuill = true;
            textarea.value = html;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            textarea.dispatchEvent(new Event('change', { bubbles: true }));
            syncingFromQuill = false;
        };

        const syncQuillFromTextarea = () => {
            if (syncingFromQuill) {
                return;
            }
            const source = stripInlineStyles(readTextareaHtml(textarea));
            const current = stripInlineStyles(normalizeHtml(readEditorHtml()));
            if (source === current) {
                return;
            }
            syncingFromTextarea = true;
            if (source) {
                quill.clipboard.dangerouslyPasteHTML(source);
            } else {
                quill.setText('');
            }
            syncingFromTextarea = false;
        };

        quill.on('text-change', syncTextareaFromQuill);
        textarea.addEventListener('input', syncQuillFromTextarea);
        textarea.addEventListener('change', syncQuillFromTextarea);

        const toolbar = quill.getModule('toolbar');

        const insertVideo = (url) => {
            let range = quill.getSelection(true);
            if (!range && quill.__lastRange) {
                range = quill.__lastRange;
                quill.setSelection(range.index, range.length || 0, 'silent');
            }

            const fallbackIndex = Math.max(0, quill.getLength() - 1);
            const index = Number.isInteger(range?.index) ? range.index : fallbackIndex;

            if (range?.length > 0) {
                quill.deleteText(range.index, range.length, 'user');
            }

            quill.insertEmbed(index, 'video', url, 'user');
            quill.setSelection(index + 1, 0, 'silent');
            syncTextareaFromQuill();
        };

        toolbar?.addHandler('video', () => {
            window.__activeAdminQuill = quill;

            const value = window.prompt('Paste a YouTube URL to embed:', 'https://www.youtube.com/watch?v=');
            if (value === null) {
                return;
            }

            const embedUrl = resolveYouTubeEmbedUrl(value);
            if (embedUrl === '') {
                notify('warning', 'Only valid YouTube video URLs are supported.');
                return;
            }

            insertVideo(embedUrl);
            notify('success', 'YouTube video embedded.');
        });

        if (imageUploadUrl !== '') {
            let imageUploadInFlight = false;

            const updateImageNode = (imageNode, url, altText = '') => {
                if (!(imageNode instanceof HTMLImageElement)) {
                    return false;
                }

                imageNode.setAttribute('src', url);
                imageNode.setAttribute('alt', altText !== '' ? altText : (imageNode.getAttribute('alt') || ''));
                imageNode.setAttribute('loading', 'lazy');
                imageNode.setAttribute('decoding', 'async');
                selectImageNode(imageNode);
                syncTextareaFromQuill();

                return true;
            };

            const insertOrReplaceImage = (url, altText = '') => {
                if (quill.__selectedImage instanceof HTMLImageElement && quill.root.contains(quill.__selectedImage)) {
                    return updateImageNode(quill.__selectedImage, url, altText);
                }

                let range = quill.getSelection(true);
                if (!range && quill.__lastRange) {
                    range = quill.__lastRange;
                    quill.setSelection(range.index, range.length || 0, 'silent');
                }

                const fallbackIndex = Math.max(0, quill.getLength() - 1);
                const index = Number.isInteger(range?.index) ? range.index : fallbackIndex;

                if (range?.length > 0) {
                    quill.deleteText(range.index, range.length, 'user');
                }

                quill.insertEmbed(index, 'image', url, 'user');

                const [leaf] = quill.getLeaf(index);
                if (leaf?.domNode instanceof HTMLImageElement) {
                    if (altText !== '') {
                        leaf.domNode.setAttribute('alt', altText);
                    }
                    leaf.domNode.setAttribute('loading', 'lazy');
                    leaf.domNode.setAttribute('decoding', 'async');
                    selectImageNode(leaf.domNode);
                }

                quill.setSelection(index + 1, 0, 'silent');
                syncTextareaFromQuill();
                clearSelectedImage();
                return false;
            };

            const uploadImage = async (file) => {
                if (!(file instanceof File) || imageUploadInFlight) {
                    return;
                }

                imageUploadInFlight = true;
                wrapper.classList.add('is-uploading-image');
                window.__activeAdminQuill = quill;

                const formData = new FormData();
                formData.append('image', file);

                try {
                    const response = await fetch(imageUploadUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
                    });

                    let payload = null;
                    try {
                        payload = await response.json();
                    } catch (error) {
                        payload = null;
                    }

                    if (!response.ok) {
                        const message =
                            payload?.message ||
                            (typeof payload?.errors === 'object' ? Object.values(payload.errors)[0]?.[0] : null) ||
                            'Image upload failed.';
                        throw new Error(String(message));
                    }

                    const imageUrl = String(payload?.url || '').trim();
                    if (imageUrl === '') {
                        throw new Error('Image upload failed.');
                    }

                    const didReplace = insertOrReplaceImage(imageUrl, String(payload?.name || file.name || '').trim());
                    notify('success', didReplace ? 'Image replaced.' : 'Image uploaded.');
                } catch (error) {
                    console.error('Failed to upload editor image', error);
                    notify('danger', error instanceof Error ? error.message : 'Image upload failed.');
                } finally {
                    imageUploadInFlight = false;
                    wrapper.classList.remove('is-uploading-image');
                }
            };

            toolbar?.addHandler('image', () => {
                if (imageUploadInFlight) {
                    return;
                }

                window.__activeAdminQuill = quill;
                preserveSelectedImage = quill.__selectedImage instanceof HTMLImageElement;

                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.addEventListener('change', () => {
                    const [file] = Array.from(input.files || []);
                    preserveSelectedImage = false;
                    if (file) {
                        uploadImage(file);
                    }
                }, { once: true });
                window.addEventListener('focus', () => {
                    preserveSelectedImage = false;
                }, { once: true });
                input.click();
            });
        }

        // Keep editor in sync when the textarea is patched by Livewire without input events.
        const valueObserver = new MutationObserver(() => {
            syncQuillFromTextarea();
        });
        valueObserver.observe(textarea, {
            childList: true,
            characterData: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['value'],
        });

        // Hydration can complete just after Quill mount, so do a delayed pull as well.
        setTimeout(syncQuillFromTextarea, 0);
        setTimeout(syncQuillFromTextarea, 200);
    };

    const bindAll = (root) => {
        if (!root) {
            return;
        }
        if (root instanceof HTMLTextAreaElement && root.matches(selector)) {
            bindElement(root);
        }
        root.querySelectorAll?.(selector).forEach(bindElement);
    };

    bindAll(document);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node instanceof HTMLElement || node instanceof HTMLTextAreaElement) {
                    bindAll(node);
                }
            });

            if (mutation.removedNodes.length > 0 && mutation.target instanceof HTMLElement) {
                bindAll(mutation.target);
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });

};

const initMediaImageEditor = () => {
    if (!document.body || document.body.dataset.mediaImageEditorReady === '1') {
        return;
    }

    const currentPath = window.location?.pathname || '';
    const isAdminPath = currentPath === '/admin' || currentPath.startsWith('/admin/');
    if (!isAdminPath) {
        return;
    }

    document.body.dataset.mediaImageEditorReady = '1';

    const clamp = (value, min = 0, max = 100) => Math.max(min, Math.min(max, value));
    const toFixed = (value) => Number.parseFloat(String(value || 0)).toFixed(1);

    const createModal = () => {
        const existing = document.getElementById('admin-image-edit-overlay');
        if (existing instanceof HTMLElement) {
            return existing;
        }

        const overlay = document.createElement('div');
        overlay.id = 'admin-image-edit-overlay';
        overlay.className = 'admin-image-edit-overlay';
        overlay.setAttribute('aria-hidden', 'true');
        overlay.innerHTML = `
            <div class="admin-image-edit-modal" role="dialog" aria-modal="true" aria-labelledby="admin-image-edit-title">
                <div class="admin-image-edit-header">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Image Edit</p>
                        <h2 id="admin-image-edit-title" class="mt-1 text-base font-semibold tracking-tight text-slate-900">Crop & Focus</h2>
                    </div>
                    <button type="button" id="admin-image-edit-close" class="rounded-md border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100">Close</button>
                </div>
                <div class="admin-image-edit-body">
                    <div class="admin-image-edit-canvas-wrap">
                        <div class="admin-image-edit-canvas" id="admin-image-edit-canvas"></div>
                        <span class="admin-image-focal-dot" id="admin-image-focal-dot" aria-hidden="true"></span>
                    </div>
                    <div class="admin-image-edit-side">
                        <label class="admin-switch">
                            <input type="checkbox" id="admin-image-edit-crop-enabled" />
                            <span class="admin-switch-slider" aria-hidden="true"></span>
                            <span>Use crop box</span>
                        </label>
                        <div class="admin-image-edit-meta">
                            <div class="admin-image-meta-row"><span>Focal X</span><strong id="admin-image-meta-focal-x">50.0%</strong></div>
                            <div class="admin-image-meta-row"><span>Focal Y</span><strong id="admin-image-meta-focal-y">50.0%</strong></div>
                            <div class="admin-image-meta-row"><span>Crop X</span><strong id="admin-image-meta-crop-x">0.0%</strong></div>
                            <div class="admin-image-meta-row"><span>Crop Y</span><strong id="admin-image-meta-crop-y">0.0%</strong></div>
                            <div class="admin-image-meta-row"><span>Crop W</span><strong id="admin-image-meta-crop-w">100.0%</strong></div>
                            <div class="admin-image-meta-row"><span>Crop H</span><strong id="admin-image-meta-crop-h">100.0%</strong></div>
                        </div>
                        <p class="text-xs text-slate-500">Tip: drag the crop box to frame conversion area. Click image to set focus point.</p>
                    </div>
                </div>
                <div class="admin-image-edit-footer">
                    <button type="button" id="admin-image-edit-reset" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Reset</button>
                    <button type="button" id="admin-image-edit-cancel" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                    <button type="button" id="admin-image-edit-apply" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Apply</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);
        return overlay;
    };

    const overlay = createModal();
    const closeButton = document.getElementById('admin-image-edit-close');
    const cancelButton = document.getElementById('admin-image-edit-cancel');
    const resetButton = document.getElementById('admin-image-edit-reset');
    const applyButton = document.getElementById('admin-image-edit-apply');
    const canvasHost = document.getElementById('admin-image-edit-canvas');
    const canvasWrap = canvasHost?.closest('.admin-image-edit-canvas-wrap');
    const focalDot = document.getElementById('admin-image-focal-dot');
    const cropEnabledInput = document.getElementById('admin-image-edit-crop-enabled');

    const metaFocalX = document.getElementById('admin-image-meta-focal-x');
    const metaFocalY = document.getElementById('admin-image-meta-focal-y');
    const metaCropX = document.getElementById('admin-image-meta-crop-x');
    const metaCropY = document.getElementById('admin-image-meta-crop-y');
    const metaCropW = document.getElementById('admin-image-meta-crop-w');
    const metaCropH = document.getElementById('admin-image-meta-crop-h');

    if (
        !(overlay instanceof HTMLElement) ||
        !(closeButton instanceof HTMLButtonElement) ||
        !(cancelButton instanceof HTMLButtonElement) ||
        !(resetButton instanceof HTMLButtonElement) ||
        !(applyButton instanceof HTMLButtonElement) ||
        !(canvasHost instanceof HTMLElement) ||
        !(canvasWrap instanceof HTMLElement) ||
        !(focalDot instanceof HTMLElement) ||
        !(cropEnabledInput instanceof HTMLInputElement) ||
        !(metaFocalX instanceof HTMLElement) ||
        !(metaFocalY instanceof HTMLElement) ||
        !(metaCropX instanceof HTMLElement) ||
        !(metaCropY instanceof HTMLElement) ||
        !(metaCropW instanceof HTMLElement) ||
        !(metaCropH instanceof HTMLElement)
    ) {
        return;
    }

    /** @type {Cropper|null} */
    let cropper = null;
    let openState = null;
    let busy = false;

    const parseBool = (value) => {
        const raw = String(value ?? '').toLowerCase();
        return raw === '1' || raw === 'true' || raw === 'yes';
    };

    const updateMeta = (state) => {
        metaFocalX.textContent = `${toFixed(state.focalX)}%`;
        metaFocalY.textContent = `${toFixed(state.focalY)}%`;
        metaCropX.textContent = `${toFixed(state.cropX)}%`;
        metaCropY.textContent = `${toFixed(state.cropY)}%`;
        metaCropW.textContent = `${toFixed(state.cropWidth)}%`;
        metaCropH.textContent = `${toFixed(state.cropHeight)}%`;
    };

    const updateFocalDot = (state) => {
        focalDot.style.left = `${state.focalX}%`;
        focalDot.style.top = `${state.focalY}%`;
    };

    const toNaturalPercent = (state, cropData) => {
        if (!state.imageNaturalWidth || !state.imageNaturalHeight) {
            return null;
        }

        const x = clamp((cropData.x / state.imageNaturalWidth) * 100);
        const y = clamp((cropData.y / state.imageNaturalHeight) * 100);
        const width = clamp((cropData.width / state.imageNaturalWidth) * 100, 1, 100);
        const height = clamp((cropData.height / state.imageNaturalHeight) * 100, 1, 100);

        return { x, y, width, height };
    };

    const setCropDataFromPercent = (state) => {
        if (!cropper || !state.imageNaturalWidth || !state.imageNaturalHeight) {
            return;
        }

        cropper.setData({
            x: (state.cropX / 100) * state.imageNaturalWidth,
            y: (state.cropY / 100) * state.imageNaturalHeight,
            width: (state.cropWidth / 100) * state.imageNaturalWidth,
            height: (state.cropHeight / 100) * state.imageNaturalHeight,
        });
    };

    const syncCropperMode = () => {
        if (!cropper || !openState) {
            return;
        }

        if (openState.cropEnabled) {
            cropper.setDragMode('crop');
            cropper.crop();
            setCropDataFromPercent(openState);
        } else {
            cropper.clear();
            cropper.setDragMode('move');
        }
    };

    const destroyCropper = () => {
        if (!cropper) {
            return;
        }

        cropper.destroy();
        cropper = null;
        canvasHost.innerHTML = '';
    };

    const close = () => {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        destroyCropper();
        openState = null;
        busy = false;
        applyButton.disabled = false;
    };

    const open = (button) => {
        const imageUrl = String(button.dataset.imageUrl || '').trim();
        const mediaId = Number.parseInt(String(button.dataset.mediaId || ''), 10);
        if (!imageUrl || Number.isNaN(mediaId)) {
            return;
        }

        const state = {
            trigger: button,
            mediaId,
            imageUrl,
            wireId: button.closest('[wire\\:id]')?.getAttribute('wire:id') || '',
            focalX: clamp(Number.parseFloat(String(button.dataset.focalX || '50')) || 50),
            focalY: clamp(Number.parseFloat(String(button.dataset.focalY || '50')) || 50),
            cropEnabled: parseBool(button.dataset.cropEnabled),
            cropX: clamp(Number.parseFloat(String(button.dataset.cropX || '0')) || 0),
            cropY: clamp(Number.parseFloat(String(button.dataset.cropY || '0')) || 0),
            cropWidth: clamp(Number.parseFloat(String(button.dataset.cropWidth || '100')) || 100, 1, 100),
            cropHeight: clamp(Number.parseFloat(String(button.dataset.cropHeight || '100')) || 100, 1, 100),
            imageNaturalWidth: 0,
            imageNaturalHeight: 0,
        };

        openState = state;
        cropEnabledInput.checked = state.cropEnabled;
        updateMeta(state);
        updateFocalDot(state);

        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        applyButton.disabled = false;
        busy = false;

        destroyCropper();
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = '';
        img.className = 'admin-image-edit-image';
        canvasHost.appendChild(img);

        cropper = new Cropper(img, {
            viewMode: 1,
            autoCropArea: 1,
            background: false,
            responsive: true,
            zoomable: false,
            scalable: false,
            rotatable: false,
            guides: true,
            center: true,
            movable: true,
            ready() {
                if (!openState || !cropper) {
                    return;
                }
                const imageData = cropper.getImageData();
                openState.imageNaturalWidth = imageData.naturalWidth || 0;
                openState.imageNaturalHeight = imageData.naturalHeight || 0;
                syncCropperMode();
            },
            crop(event) {
                if (!openState || !cropper || !openState.cropEnabled) {
                    return;
                }
                const next = toNaturalPercent(openState, event.detail);
                if (!next) {
                    return;
                }

                openState.cropX = next.x;
                openState.cropY = next.y;
                openState.cropWidth = next.width;
                openState.cropHeight = next.height;
                openState.focalX = clamp(next.x + next.width / 2);
                openState.focalY = clamp(next.y + next.height / 2);
                updateMeta(openState);
                updateFocalDot(openState);
            },
        });
    };

    const setFocalFromClick = (event) => {
        if (!openState || !canvasWrap) {
            return;
        }

        const image = canvasHost.querySelector('img');
        if (!(image instanceof HTMLImageElement)) {
            return;
        }

        const rect = image.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) {
            return;
        }

        const clickX = ((event.clientX - rect.left) / rect.width) * 100;
        const clickY = ((event.clientY - rect.top) / rect.height) * 100;
        openState.focalX = clamp(clickX);
        openState.focalY = clamp(clickY);
        updateFocalDot(openState);
        updateMeta(openState);
    };

    const notify = (type, message) => {
        window.dispatchEvent(new CustomEvent('admin:notify', {
            detail: { type, message },
        }));
    };

    const getLivewireComponent = (wireId) => {
        if (!window.Livewire) {
            return null;
        }

        if (wireId && typeof window.Livewire.find === 'function') {
            const found = window.Livewire.find(wireId);
            if (found) {
                return found;
            }
        }

        if (typeof window.Livewire.all === 'function') {
            const all = window.Livewire.all();
            if (Array.isArray(all) && all.length) {
                return all[0];
            }
        }

        return null;
    };

    const apply = async () => {
        if (busy || !openState) {
            return;
        }

        const component = getLivewireComponent(openState.wireId);
        if (!component || typeof component.call !== 'function') {
            notify('danger', 'Livewire component not available.');
            return;
        }

        busy = true;
        applyButton.disabled = true;

        try {
            await component.call('saveImageEditFromModal', openState.mediaId, {
                focal_x: openState.focalX,
                focal_y: openState.focalY,
                crop_enabled: openState.cropEnabled,
                crop_x: openState.cropX,
                crop_y: openState.cropY,
                crop_width: openState.cropWidth,
                crop_height: openState.cropHeight,
            });
            close();
        } catch (error) {
            console.error('Failed to save image edit', error);
            notify('danger', 'Failed to save crop/focus.');
            busy = false;
            applyButton.disabled = false;
        }
    };

    cropEnabledInput.addEventListener('change', () => {
        if (!openState) {
            return;
        }
        openState.cropEnabled = cropEnabledInput.checked;
        syncCropperMode();
        updateMeta(openState);
    });

    resetButton.addEventListener('click', () => {
        if (!openState) {
            return;
        }
        openState.focalX = 50;
        openState.focalY = 50;
        openState.cropEnabled = false;
        openState.cropX = 0;
        openState.cropY = 0;
        openState.cropWidth = 100;
        openState.cropHeight = 100;
        cropEnabledInput.checked = false;
        syncCropperMode();
        updateFocalDot(openState);
        updateMeta(openState);
    });

    closeButton.addEventListener('click', close);
    cancelButton.addEventListener('click', close);
    applyButton.addEventListener('click', apply);

    canvasWrap.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        if (
            target === canvasWrap ||
            target === focalDot ||
            target instanceof HTMLImageElement ||
            target.closest('.cropper-container')
        ) {
            setFocalFromClick(event);
        }
    });

    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) {
            close();
        }
    });

    window.addEventListener('keydown', (event) => {
        if (!overlay.classList.contains('is-open')) {
            return;
        }
        if (event.key === 'Escape') {
            event.preventDefault();
            close();
        }
    });

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) {
            return;
        }
        const button = target.closest('[data-image-edit-open]');
        if (!(button instanceof HTMLButtonElement)) {
            return;
        }
        event.preventDefault();
        open(button);
    });
};

const initAceInline = () => {
    if (!document.body) {
        return;
    }

    const selector = 'textarea[data-ace-inline]';

    const readTextareaValue = (textarea) => {
        if (!(textarea instanceof HTMLTextAreaElement)) {
            return '';
        }

        if ((textarea.value ?? '') !== '') {
            return textarea.value;
        }

        // Livewire can patch textarea contents without firing input/change.
        return textarea.textContent || '';
    };

    const bindElement = (textarea) => {
        if (!(textarea instanceof HTMLTextAreaElement)) {
            return;
        }
        if (textarea.dataset.aceInlineBound === '1') {
            return;
        }

        textarea.dataset.aceInlineBound = '1';

        const mount = document.createElement('div');
        mount.className = 'admin-ace-inline';
        const rows = Number.parseInt(textarea.getAttribute('rows') || '8', 10);
        const minHeight = Number.isNaN(rows) ? 220 : Math.max(180, rows * 26);
        mount.style.minHeight = `${minHeight}px`;
        textarea.insertAdjacentElement('afterend', mount);
        textarea.style.display = 'none';
        textarea.setAttribute('aria-hidden', 'true');
        textarea.tabIndex = -1;

        let editor = null;
        let syncTimer = null;
        let syncingFromEditor = false;
        let syncingFromTextarea = false;

        const syncTextareaFromEditor = () => {
            if (!editor || syncingFromTextarea) {
                return;
            }

            const value = editor.getValue();
            if (textarea.value === value) {
                return;
            }

            syncingFromEditor = true;
            textarea.value = value;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            textarea.dispatchEvent(new Event('change', { bubbles: true }));
            syncingFromEditor = false;
        };

        const scheduleSyncTextarea = () => {
            if (syncTimer) {
                clearTimeout(syncTimer);
            }
            syncTimer = setTimeout(syncTextareaFromEditor, 120);
        };

        const syncEditorFromTextarea = () => {
            if (!editor || syncingFromEditor) {
                return;
            }

            const value = readTextareaValue(textarea);
            if (editor.getValue() === value) {
                return;
            }

            syncingFromTextarea = true;
            editor.setValue(value, -1);
            syncingFromTextarea = false;
        };

        textarea.addEventListener('input', syncEditorFromTextarea);
        textarea.addEventListener('change', syncEditorFromTextarea);

        const valueObserver = new MutationObserver(() => {
            syncEditorFromTextarea();
        });
        valueObserver.observe(textarea, {
            childList: true,
            characterData: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['value'],
        });

        loadAce()
            .then((ace) => {
                editor = ace.edit(mount);
                editor.session.setMode('ace/mode/html');
                editor.setTheme('ace/theme/tomorrow_night');
                editor.setOptions({
                    fontSize: '13px',
                    showPrintMargin: false,
                    useSoftTabs: true,
                    tabSize: 2,
                    enableBasicAutocompletion: true,
                    enableLiveAutocompletion: true,
                });
                editor.session.setUseWorker(false);
                editor.setValue(readTextareaValue(textarea), -1);
                editor.session.on('change', scheduleSyncTextarea);
                editor.on('blur', syncTextareaFromEditor);

                // Hydration can finish right after mount.
                setTimeout(syncEditorFromTextarea, 0);
                setTimeout(syncEditorFromTextarea, 200);
            })
            .catch((error) => {
                console.error('Failed to initialize inline Ace editor', error);
                mount.remove();
                textarea.style.display = '';
                textarea.removeAttribute('aria-hidden');
                textarea.tabIndex = 0;

                if (!aceInlineFailureNotified) {
                    aceInlineFailureNotified = true;
                    window.dispatchEvent(new CustomEvent('admin:notify', {
                        detail: { type: 'danger', message: 'Inline Ace editor failed to load.' },
                    }));
                }
            });
    };

    const bindAll = (root) => {
        if (!root) {
            return;
        }
        if (root instanceof HTMLTextAreaElement && root.matches(selector)) {
            bindElement(root);
        }
        root.querySelectorAll?.(selector).forEach(bindElement);
    };

    bindAll(document);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node instanceof HTMLElement || node instanceof HTMLTextAreaElement) {
                    bindAll(node);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
};

const dashboardChartSelector = 'canvas[data-dashboard-chart]';
const dashboardChartInstances = new WeakMap();
let dashboardChartObserver = null;
let dashboardChartObserverRoot = null;

const dashboardChartError = (canvas) => canvas.parentElement?.querySelector('[data-dashboard-chart-error]');

const setDashboardChartError = (canvas, visible) => {
    const error = dashboardChartError(canvas);
    if (!(error instanceof HTMLElement)) {
        return;
    }

    error.classList.toggle('hidden', !visible);
};

const parseDashboardChartConfig = (raw) => {
    if (!raw) {
        return null;
    }

    try {
        const config = JSON.parse(raw);
        return config && typeof config === 'object' ? config : null;
    } catch (error) {
        return null;
    }
};

const destroyDashboardChart = (canvas) => {
    if (!(canvas instanceof HTMLCanvasElement)) {
        return;
    }

    const chart = dashboardChartInstances.get(canvas)?.chart ?? Chart.getChart(canvas);
    if (chart) {
        chart.destroy();
    }

    dashboardChartInstances.delete(canvas);
};

const bindDashboardChart = (canvas) => {
    if (!(canvas instanceof HTMLCanvasElement)) {
        return;
    }

    const payload = canvas.getAttribute('data-chart-payload');
    const config = parseDashboardChartConfig(payload);
    if (!config) {
        destroyDashboardChart(canvas);
        setDashboardChartError(canvas, true);
        return;
    }

    const existing = dashboardChartInstances.get(canvas);
    if (existing?.payload === payload && Chart.getChart(canvas) === existing.chart) {
        setDashboardChartError(canvas, false);
        return;
    }

    destroyDashboardChart(canvas);

    const context = canvas.getContext('2d');
    if (!context) {
        setDashboardChartError(canvas, true);
        return;
    }

    try {
        const chart = new Chart(context, {
            type: config.type || 'line',
            data: config.data || { labels: [], datasets: [] },
            options: config.options || {},
        });
        dashboardChartInstances.set(canvas, { chart, payload });
        setDashboardChartError(canvas, false);
    } catch (error) {
        setDashboardChartError(canvas, true);
        console.error('Failed to render dashboard chart', error);
    }
};

const bindDashboardCharts = (root) => {
    if (!root) {
        return;
    }
    if (root instanceof HTMLCanvasElement && root.matches(dashboardChartSelector)) {
        bindDashboardChart(root);
    }
    root.querySelectorAll?.(dashboardChartSelector).forEach(bindDashboardChart);
};

const destroyDashboardChartsFromNode = (node) => {
    if (!node) {
        return;
    }
    if (node instanceof HTMLCanvasElement && node.matches(dashboardChartSelector)) {
        destroyDashboardChart(node);
    }
    if (node instanceof HTMLElement) {
        node.querySelectorAll(dashboardChartSelector).forEach(destroyDashboardChart);
    }
};

const initDashboardCharts = () => {
    if (!document.body) {
        return;
    }

    bindDashboardCharts(document);

    if (dashboardChartObserver && dashboardChartObserverRoot === document.body) {
        return;
    }

    dashboardChartObserver?.disconnect();
    dashboardChartObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.target instanceof HTMLCanvasElement) {
                bindDashboardChart(mutation.target);
                return;
            }

            mutation.removedNodes.forEach(destroyDashboardChartsFromNode);
            mutation.addedNodes.forEach((node) => {
                if (node instanceof HTMLElement || node instanceof HTMLCanvasElement) {
                    bindDashboardCharts(node);
                }
            });
        });
    });

    dashboardChartObserver.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['data-chart-payload'],
    });
    dashboardChartObserverRoot = document.body;
};

const initFrontDesktopHeader = () => {
    const stickyHeader = document.querySelector('[data-front-sticky-header]');
    const stickyBar = document.querySelector('.front-header-sticky') ?? stickyHeader;
    const metaBar = document.querySelector('.front-header-meta');
    const root = document.querySelector('[data-mobile-menu-root]');
    const panel = root?.querySelector('[data-mobile-menu-panel]');
    const overlay = root?.querySelector('[data-mobile-menu-close]');
    const openButtons = document.querySelectorAll('[data-mobile-menu-open]');
    const closeButtons = root?.querySelectorAll('[data-mobile-menu-close]') ?? [];
    const searchPanel = document.querySelector('[data-header-search-panel]');
    const searchToggles = document.querySelectorAll('[data-header-search-toggle]');
    const searchInput = document.querySelector('[data-header-search-input]');
    const searchForm = searchPanel?.querySelector('[data-header-search-form]');
    const searchSuggestions = searchPanel?.querySelector('[data-header-search-suggestions]');
    const searchSuggestEndpoint = searchForm instanceof HTMLFormElement
        ? (searchForm.dataset.searchSuggestEndpoint || '')
        : '';
    let searchDebounceTimer = 0;
    let searchRequestId = 0;
    let searchAbortController = null;
    const syncHeaderOffsetVar = () => {
        if (!(stickyBar instanceof HTMLElement)) {
            return;
        }
        const stickyHeight = Math.max(0, Math.floor(stickyBar.offsetHeight));
        const metaHeight = metaBar instanceof HTMLElement && window.getComputedStyle(metaBar).display !== 'none'
            ? Math.max(0, Math.floor(metaBar.getBoundingClientRect().height))
            : 0;
        const totalHeaderHeight = stickyHeight + metaHeight;
        document.documentElement.style.setProperty('--front-header-offset', `${totalHeaderHeight}px`);
    };

    const closeMenu = () => {
        if (!root || !panel || !overlay) {
            return;
        }

        root.classList.add('pointer-events-none');
        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        panel.classList.add('-translate-x-full');
        panel.classList.remove('translate-x-0');
        document.body.classList.remove('overflow-hidden');
    };

    const openMenu = () => {
        if (!root || !panel || !overlay) {
            return;
        }

        closeSearch();
        root.classList.remove('pointer-events-none');
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('-translate-x-full');
        panel.classList.add('translate-x-0');
        document.body.classList.add('overflow-hidden');
    };

    const setSearchState = (isOpen) => {
        stickyHeader?.classList.toggle('is-search-open', isOpen);
        document.body?.classList.toggle('front-header-search-open', isOpen);
    };

    const hideSearchSuggestions = () => {
        if (!(searchSuggestions instanceof HTMLElement)) {
            return;
        }

        searchSuggestions.classList.add('hidden');
        searchSuggestions.replaceChildren();
    };

    const createSearchSuggestionItem = (item) => {
        const link = document.createElement('a');
        link.className = 'front-search-suggestion-link';
        link.href = item.url || '#';

        if (item.image_url) {
            const mediaWrap = document.createElement('span');
            mediaWrap.className = 'front-search-suggestion-media';

            const image = document.createElement('img');
            image.src = item.image_url;
            image.alt = item.title || '';
            image.loading = 'lazy';
            image.decoding = 'async';
            mediaWrap.append(image);
            link.append(mediaWrap);
        }

        const body = document.createElement('span');
        body.className = 'front-search-suggestion-body';

        if (item.eyebrow || item.meta) {
            const meta = document.createElement('span');
            meta.className = 'front-search-suggestion-meta';

            if (item.eyebrow) {
                const eyebrow = document.createElement('span');
                eyebrow.textContent = item.eyebrow;
                meta.append(eyebrow);
            }

            if (item.meta) {
                const metaText = document.createElement('span');
                metaText.textContent = item.meta;
                meta.append(metaText);
            }

            body.append(meta);
        }

        const title = document.createElement('strong');
        title.className = 'front-search-suggestion-title';
        title.textContent = item.title || '';
        body.append(title);

        if (item.excerpt) {
            const excerpt = document.createElement('span');
            excerpt.className = 'front-search-suggestion-excerpt';
            excerpt.textContent = item.excerpt;
            body.append(excerpt);
        }

        link.append(body);

        return link;
    };

    const renderSearchSuggestions = (payload) => {
        if (!(searchSuggestions instanceof HTMLElement)) {
            return;
        }

        searchSuggestions.replaceChildren();

        const sections = Array.isArray(payload?.sections) ? payload.sections : [];

        if (!sections.length) {
            const empty = document.createElement('div');
            empty.className = 'front-search-suggestion-empty';
            empty.textContent = window.CodexSearchLabels?.autosuggestEmpty || 'No results.';
            searchSuggestions.append(empty);
            searchSuggestions.classList.remove('hidden');
            return;
        }

        sections.forEach((section) => {
            const sectionWrap = document.createElement('section');
            sectionWrap.className = 'front-search-suggestion-section';

            const heading = document.createElement('div');
            heading.className = 'front-search-suggestion-section-head';

            const title = document.createElement('h3');
            title.textContent = section.label || '';
            heading.append(title);

            if (typeof section.total_count === 'number') {
                const count = document.createElement('span');
                count.textContent = String(section.total_count);
                heading.append(count);
            }

            sectionWrap.append(heading);

            const list = document.createElement('div');
            list.className = 'front-search-suggestion-list';

            const items = Array.isArray(section.items) ? section.items : [];
            items.forEach((item) => {
                list.append(createSearchSuggestionItem(item));
            });

            sectionWrap.append(list);
            searchSuggestions.append(sectionWrap);
        });

        if (payload?.results_url) {
            const footer = document.createElement('div');
            footer.className = 'front-search-suggestion-footer';

            const viewAll = document.createElement('a');
            viewAll.href = payload.results_url;
            viewAll.className = 'front-search-suggestion-all';
            viewAll.textContent = window.CodexSearchLabels?.showMore || 'Show more';
            footer.append(viewAll);

            searchSuggestions.append(footer);
        }

        searchSuggestions.classList.remove('hidden');
    };

    const fetchSearchSuggestions = (rawQuery) => {
        const query = typeof rawQuery === 'string' ? rawQuery.trim() : '';

        window.clearTimeout(searchDebounceTimer);

        if (!(searchInput instanceof HTMLInputElement) || query.length < 2 || searchSuggestEndpoint === '') {
            if (searchAbortController) {
                searchAbortController.abort();
                searchAbortController = null;
            }
            hideSearchSuggestions();
            return;
        }

        searchDebounceTimer = window.setTimeout(async () => {
            if (searchAbortController) {
                searchAbortController.abort();
            }

            const currentRequestId = ++searchRequestId;
            searchAbortController = new AbortController();

            try {
                const url = new URL(searchSuggestEndpoint, window.location.origin);
                url.searchParams.set('q', query);

                const response = await window.fetch(url.toString(), {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: searchAbortController.signal,
                });

                if (!response.ok) {
                    throw new Error(`Search suggest failed with status ${response.status}`);
                }

                const payload = await response.json();

                if (currentRequestId !== searchRequestId || searchInput.value.trim() !== query) {
                    return;
                }

                renderSearchSuggestions(payload);
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    console.error('Failed to fetch search suggestions', error);
                }
                hideSearchSuggestions();
            }
        }, 180);
    };

    const closeSearch = () => {
        if (!searchPanel) {
            return;
        }
        searchPanel.classList.remove('is-open');
        hideSearchSuggestions();
        setSearchState(false);
    };

    const openSearch = () => {
        if (!searchPanel) {
            return;
        }
        closeMenu();
        searchPanel.classList.add('is-open');
        setSearchState(true);
        if (searchInput instanceof HTMLInputElement) {
            requestAnimationFrame(() => {
                searchInput.focus();
                if (searchInput.value.trim().length >= 2) {
                    fetchSearchSuggestions(searchInput.value);
                }
            });
        }
    };

    const toggleSearch = () => {
        if (!searchPanel) {
            return;
        }
        if (searchPanel.classList.contains('is-open')) {
            closeSearch();
            return;
        }
        openSearch();
    };

    openButtons.forEach((button) => {
        if (button.dataset.boundMenuOpen === '1') return;
        button.dataset.boundMenuOpen = '1';
        button.addEventListener('click', openMenu);
    });

    closeButtons.forEach((button) => {
        if (!(button instanceof HTMLElement) || button.dataset.boundMenuClose === '1') return;
        button.dataset.boundMenuClose = '1';
        button.addEventListener('click', closeMenu);
    });

    root?.querySelectorAll('a').forEach((link) => {
        if (!(link instanceof HTMLElement) || link.dataset.boundMenuLink === '1') return;
        link.dataset.boundMenuLink = '1';
        link.addEventListener('click', closeMenu);
    });

    searchToggles.forEach((toggle) => {
        if (toggle instanceof HTMLElement && toggle.dataset.boundSearchToggle !== '1') {
            toggle.dataset.boundSearchToggle = '1';
            toggle.addEventListener('click', toggleSearch);
        }
    });

    if (searchInput instanceof HTMLInputElement && searchInput.dataset.boundSearchInput !== '1') {
        searchInput.dataset.boundSearchInput = '1';
        searchInput.addEventListener('input', (event) => {
            fetchSearchSuggestions(event.currentTarget?.value || '');
        });
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim().length >= 2) {
                fetchSearchSuggestions(searchInput.value);
            }
        });
    }

    if (searchForm instanceof HTMLFormElement && searchForm.dataset.boundSearchSubmit !== '1') {
        searchForm.dataset.boundSearchSubmit = '1';
        searchForm.addEventListener('submit', (event) => {
            if (!(searchInput instanceof HTMLInputElement)) {
                return;
            }

            if (searchInput.value.trim() === '') {
                event.preventDefault();
                searchInput.focus();
                hideSearchSuggestions();
            }
        });
    }

    if (document.body && document.body.dataset.frontHeaderEscBound !== '1') {
        document.body.dataset.frontHeaderEscBound = '1';
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
                closeSearch();
            }
        });
    }

    if (document.body && document.body.dataset.frontHeaderClickBound !== '1') {
        document.body.dataset.frontHeaderClickBound = '1';
        document.addEventListener('click', (event) => {
            const target = event.target;

            if (!(target instanceof Node)) {
                return;
            }

            const clickedInsideSearch = searchPanel instanceof HTMLElement && searchPanel.contains(target);
            const clickedToggle = Array.from(searchToggles).some((toggle) => toggle instanceof HTMLElement && toggle.contains(target));

            if (!clickedInsideSearch && !clickedToggle) {
                hideSearchSuggestions();
            }
        });
    }

    if (stickyBar instanceof HTMLElement) {
        if (stickyHeader instanceof HTMLElement) {
            stickyHeader.classList.remove('is-compact');
        }
        syncHeaderOffsetVar();
    }
};

const initHeroStatCounters = () => {
    const counters = document.querySelectorAll('[data-count-up]');
    if (!counters.length) {
        return;
    }

    const prefersReducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false;

    const formatValue = (element, value) => {
        const suffix = element.dataset.countSuffix || '';
        return `${Math.round(value)}${suffix}`;
    };

    const finalizeCounter = (element) => {
        const target = Number.parseInt(element.dataset.countTo || '0', 10);
        element.textContent = formatValue(element, Number.isNaN(target) ? 0 : target);
        element.dataset.countAnimated = '1';
        delete element.dataset.countAnimating;
    };

    const animateCounter = (element) => {
        if (element.dataset.countAnimated === '1' || element.dataset.countAnimating === '1') {
            return;
        }

        const target = Number.parseInt(element.dataset.countTo || '0', 10);
        if (Number.isNaN(target) || target <= 0 || prefersReducedMotion) {
            finalizeCounter(element);
            return;
        }

        const duration = Number.parseInt(element.dataset.countDuration || '2200', 10);
        const safeDuration = Number.isNaN(duration) ? 2200 : Math.max(900, duration);
        element.dataset.countAnimating = '1';

        const startTime = performance.now();
        const step = (currentTime) => {
            const progress = Math.min((currentTime - startTime) / safeDuration, 1);
            const easedProgress = 0.5 - (Math.cos(Math.PI * progress) / 2);
            const currentValue = target * easedProgress;

            element.textContent = formatValue(element, currentValue);

            if (progress < 1) {
                window.requestAnimationFrame(step);
                return;
            }

            finalizeCounter(element);
        };

        window.requestAnimationFrame(step);
    };

    counters.forEach((counter) => {
        if (!(counter instanceof HTMLElement)) {
            return;
        }

        if (counter.dataset.countAnimated !== '1' && counter.dataset.countPrepared !== '1') {
            counter.textContent = formatValue(counter, 0);
            counter.dataset.countPrepared = '1';
        }
    });

    if (!('IntersectionObserver' in window) || prefersReducedMotion) {
        counters.forEach((counter) => {
            if (counter instanceof HTMLElement) {
                finalizeCounter(counter);
            }
        });
        return;
    }

    if (!window.__frontHeroStatObserver) {
        window.__frontHeroStatObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const element = entry.target;
                if (element instanceof HTMLElement) {
                    animateCounter(element);
                }
                observer.unobserve(element);
            });
        }, {
            threshold: 0.35,
            rootMargin: '0px 0px -8% 0px',
        });
    }

    counters.forEach((counter) => {
        if (!(counter instanceof HTMLElement) || counter.dataset.countAnimated === '1') {
            return;
        }

        if (counter.dataset.countObserved === '1') {
            return;
        }

        counter.dataset.countObserved = '1';
        window.__frontHeroStatObserver.observe(counter);
    });
};

const fallbackAnimateScrollTo = (targetTop, options = {}) => {
    const resolveTarget = (target) => {
        if (typeof target === 'number' && Number.isFinite(target)) {
            return target;
        }

        if (target instanceof HTMLElement) {
            return window.pageYOffset + target.getBoundingClientRect().top;
        }

        if (typeof target === 'string') {
            if (target === 'top' || target === 'start') {
                return 0;
            }

            const matchedElement = document.querySelector(target);
            if (matchedElement instanceof HTMLElement) {
                return window.pageYOffset + matchedElement.getBoundingClientRect().top;
            }

            const parsedNumber = Number(target);
            if (Number.isFinite(parsedNumber)) {
                return parsedNumber;
            }
        }

        return null;
    };

    const resolvedTarget = resolveTarget(targetTop);
    if (resolvedTarget === null) {
        return;
    }

    window.scrollTo({
        top: Math.max(0, Math.round(resolvedTarget)),
        behavior: options.immediate === true ? 'auto' : 'smooth',
    });

    if (typeof options.onComplete === 'function') {
        window.setTimeout(() => options.onComplete(), options.immediate === true ? 0 : 320);
    }
};

const initFrontSmoothScroll = () => {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return;
    }

    const frontRoot = document.querySelector('.front-desktop-shell');
    if (!(frontRoot instanceof HTMLElement)) {
        return;
    }

    if (typeof window.__frontAnimateScrollTo === 'function') {
        return;
    }

    window.__frontAnimateScrollTo = fallbackAnimateScrollTo;
};

const disableLegacyPwaRuntime = () => {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        localStorage.removeItem('Appkit-PWA-Prompt');
        localStorage.removeItem('Appkit-PWA-Timeout-Value');
    } catch (error) {
        // Ignore localStorage access failures.
    }

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations()
            .then((registrations) => {
                registrations.forEach((registration) => {
                    const scriptUrl = registration.active?.scriptURL
                        ?? registration.waiting?.scriptURL
                        ?? registration.installing?.scriptURL
                        ?? '';

                    if (scriptUrl.includes('/_service-worker.js') || scriptUrl.includes('front-theme/_service-worker.js')) {
                        registration.unregister().catch(() => {});
                    }
                });
            })
            .catch(() => {});
    }

    if ('caches' in window) {
        caches.keys()
            .then((names) => Promise.all(
                names
                    .filter((name) => /workbox|appkit|pwa|service-worker/i.test(name))
                    .map((name) => caches.delete(name)),
            ))
            .catch(() => {});
    }
};

const initFrontVisualEffects = () => {
    // Temporarily disable space banner effects to keep scroll/perf smooth.
    return;
};

const initAdminMessageNotifications = () => {
    const selector = '[data-admin-message-notifications]';
    const syncExpandedState = (dropdown) => {
        if (!(dropdown instanceof HTMLDetailsElement)) {
            return;
        }

        const trigger = dropdown.querySelector('summary');
        if (trigger instanceof HTMLElement) {
            trigger.setAttribute('aria-expanded', dropdown.open ? 'true' : 'false');
        }
    };

    document.querySelectorAll(selector).forEach(syncExpandedState);

    if (document.documentElement.dataset.adminMessageNotificationsReady === '1') {
        return;
    }
    document.documentElement.dataset.adminMessageNotificationsReady = '1';

    document.addEventListener('toggle', (event) => {
        const dropdown = event.target;
        if (!(dropdown instanceof HTMLDetailsElement) || !dropdown.matches(selector)) {
            return;
        }

        syncExpandedState(dropdown);

        if (dropdown.open) {
            dropdown.parentElement?.querySelectorAll('details[open]').forEach((otherDropdown) => {
                if (otherDropdown !== dropdown && otherDropdown instanceof HTMLDetailsElement) {
                    otherDropdown.open = false;
                }
            });
        }
    }, true);

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Node)) {
            return;
        }

        document.querySelectorAll(`${selector}[open]`).forEach((dropdown) => {
            if (dropdown instanceof HTMLDetailsElement && !dropdown.contains(event.target)) {
                dropdown.open = false;
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const dropdown = document.querySelector(`${selector}[open]`);
        if (!(dropdown instanceof HTMLDetailsElement)) {
            return;
        }

        dropdown.open = false;
        const trigger = dropdown.querySelector('summary');
        if (trigger instanceof HTMLElement) {
            trigger.focus();
        }
    });
};

const initAdminDirtyForms = () => {
    const body = document.body;
    if (!body || body.dataset.adminDirtyFormsReady === '1') {
        return;
    }

    body.dataset.adminDirtyFormsReady = '1';

    const dirtyForms = () => Array.from(document.querySelectorAll('form[data-admin-dirty-form][data-admin-dirty="true"]'));
    const markDirty = (form) => {
        if (form instanceof HTMLFormElement && form.dataset.adminSubmitting !== 'true') {
            form.dataset.adminDirty = 'true';
        }
    };

    const formFromEvent = (event) => event.target instanceof Element
        ? event.target.closest('form[data-admin-dirty-form]')
        : null;

    document.addEventListener('input', (event) => markDirty(formFromEvent(event)), true);
    document.addEventListener('change', (event) => markDirty(formFromEvent(event)), true);

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const leaveTrigger = event.target.closest('[data-admin-leave]');
        if (leaveTrigger) {
            const forms = dirtyForms();
            if (forms.length === 0) {
                return;
            }

            const shouldLeave = window.confirm('Imate nespremljene izmjene. Želite li napustiti obrazac?');
            if (!shouldLeave) {
                event.preventDefault();
                event.stopImmediatePropagation();
                return;
            }

            forms.forEach((form) => {
                form.dataset.adminDirty = 'false';
                form.dataset.adminSubmitting = 'true';
            });
            return;
        }

        const actionButton = event.target.closest('form[data-admin-dirty-form] button[wire\\:click]');
        if (!(actionButton instanceof HTMLButtonElement)) {
            return;
        }

        const action = actionButton.getAttribute('wire:click') || '';
        if (action.includes('form.') || /(?:add|remove|move|generate|quickCreate)/.test(action)) {
            markDirty(actionButton.closest('form[data-admin-dirty-form]'));
        }
    }, true);

    document.addEventListener('submit', (event) => {
        const form = formFromEvent(event);
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        form.dataset.adminSubmitting = 'true';
        window.setTimeout(() => {
            if (document.contains(form)) {
                form.dataset.adminSubmitting = 'false';
            }
        }, 2500);
    }, true);

    document.addEventListener('keydown', (event) => {
        if (!(event.metaKey || event.ctrlKey) || event.key.toLowerCase() !== 's') {
            return;
        }

        const form = dirtyForms()[0];
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        event.preventDefault();
        form.requestSubmit();
    });

    window.addEventListener('beforeunload', (event) => {
        const hasBlockingChanges = dirtyForms().some((form) => form.dataset.adminSubmitting !== 'true');
        if (!hasBlockingChanges) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    const revealErrorSummary = (root = document) => {
        const summary = root instanceof Element && root.matches('[data-admin-error-summary]')
            ? root
            : root.querySelector?.('[data-admin-error-summary]');
        if (!(summary instanceof HTMLElement) || summary.dataset.adminErrorRevealed === 'true') {
            return;
        }

        summary.dataset.adminErrorRevealed = 'true';
        window.requestAnimationFrame(() => {
            summary.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    };

    revealErrorSummary();
    const errorObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node instanceof Element) {
                    revealErrorSummary(node);
                }
            });
        });
    });
    errorObserver.observe(body, { childList: true, subtree: true });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initAceLauncher();
        initAceInline();
        initQuillEditors();
        initMediaImageEditor();
        initTomSelect();
        initDashboardCharts();
        initFrontSmoothScroll();
        initFrontVisualEffects();
        initFrontDesktopHeader();
        initHeroStatCounters();
        initAdminMessageNotifications();
        initAdminDirtyForms();
        disableLegacyPwaRuntime();
    }, { once: true });
} else {
    initAceLauncher();
    initAceInline();
    initQuillEditors();
    initMediaImageEditor();
    initTomSelect();
    initDashboardCharts();
    initFrontSmoothScroll();
    initFrontVisualEffects();
    initFrontDesktopHeader();
    initHeroStatCounters();
    initAdminMessageNotifications();
    initAdminDirtyForms();
    disableLegacyPwaRuntime();
}

document.addEventListener('livewire:navigated', () => {
    initTomSelect();
    initDashboardCharts();
    initFrontSmoothScroll();
    initFrontVisualEffects();
    initFrontDesktopHeader();
    initHeroStatCounters();
    initAdminMessageNotifications();
    initAdminDirtyForms();
    disableLegacyPwaRuntime();
});
