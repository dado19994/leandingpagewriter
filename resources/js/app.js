const navbar = document.querySelector('.writer-navbar');
const navLinks = document.querySelectorAll('.writer-nav-links a[href*="#"]');
const revealTargets = document.querySelectorAll('.writer-section, .writer-card, .book-card, .event-card, .press-card, .post-content');
const copyButtons = document.querySelectorAll('[data-copy-url], [data-copy-text]');
const menuToggle = document.querySelector('.writer-menu-toggle');
const modalTriggers = document.querySelectorAll('[data-modal-open]');
const modalCloseButtons = document.querySelectorAll('[data-modal-close]');
const editorFields = document.querySelectorAll('[data-editor]');
const confirmForms = document.querySelectorAll('[data-confirm]');
const flashMessages = document.querySelectorAll('[data-flash]');

const updateNavbar = () => {
    if (!navbar) {
        return;
    }

    navbar.classList.toggle('is-scrolled', window.scrollY > 18);
};

const setActiveSection = () => {
    const sections = ['featured', 'about', 'book-focus', 'books', 'events', 'blog', 'newsletter', 'press', 'contact']
        .map((id) => document.getElementById(id))
        .filter(Boolean);

    if (!sections.length) {
        return;
    }

    const current = sections.reduce((active, section) => {
        const top = section.getBoundingClientRect().top;
        return top < 150 ? section : active;
    }, sections[0]);

    navLinks.forEach((link) => {
        link.classList.toggle('is-active', link.hash === `#${current.id}`);
    });
};

if (revealTargets.length && 'IntersectionObserver' in window) {
    revealTargets.forEach((target) => target.classList.add('reveal-on-scroll'));

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.14 });

    revealTargets.forEach((target) => revealObserver.observe(target));
}

copyButtons.forEach((button) => {
    button.addEventListener('click', async () => {
        const originalText = button.textContent;
        const value = button.dataset.copyUrl || button.dataset.copyText;

        try {
            await navigator.clipboard.writeText(value);
            button.textContent = button.dataset.copyText ? 'Bio copiata' : 'Link copiato';
            button.setAttribute('aria-live', 'polite');
            button.classList.add('is-copied');
        } catch {
            button.textContent = 'Copia non riuscita';
        }

        window.setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('is-copied');
        }, 1800);
    });
});

menuToggle?.addEventListener('click', () => {
    const isOpen = navbar.classList.toggle('is-menu-open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    menuToggle.setAttribute('aria-label', isOpen ? 'Chiudi menu' : 'Apri menu');
});

navLinks.forEach((link) => {
    link.addEventListener('click', () => {
        navbar?.classList.remove('is-menu-open');
        menuToggle?.setAttribute('aria-expanded', 'false');
        menuToggle?.setAttribute('aria-label', 'Apri menu');
    });
});

const closeModal = (modal) => {
    modal?.classList.remove('is-open');
    modal?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('has-open-modal');
};

modalTriggers.forEach((trigger) => {
    trigger.addEventListener('click', () => {
        const modal = document.getElementById(trigger.dataset.modalOpen);
        modal?.classList.add('is-open');
        modal?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('has-open-modal');
        modal?.querySelector('[data-modal-close]')?.focus();
    });
});

modalCloseButtons.forEach((button) => {
    button.addEventListener('click', () => {
        closeModal(button.closest('.writer-modal'));
    });
});

document.querySelectorAll('.writer-modal').forEach((modal) => {
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal(modal);
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('.writer-modal.is-open').forEach(closeModal);
        navbar?.classList.remove('is-menu-open');
        menuToggle?.setAttribute('aria-expanded', 'false');
        menuToggle?.setAttribute('aria-label', 'Apri menu');
    }
});

const escapeHtml = (value) => value
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

const renderPreview = (value) => escapeHtml(value)
    .replace(/^### (.*)$/gm, '<h4>$1</h4>')
    .replace(/^## (.*)$/gm, '<h3>$1</h3>')
    .replace(/^# (.*)$/gm, '<h2>$1</h2>')
    .replace(/^- (.*)$/gm, '• $1')
    .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2">$1</a>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/\n{2,}/g, '</p><p>')
    .replace(/\n/g, '<br>');

const insertAroundSelection = (field, before, after = before) => {
    const start = field.selectionStart;
    const end = field.selectionEnd;
    const selected = field.value.slice(start, end) || 'testo';
    field.value = `${field.value.slice(0, start)}${before}${selected}${after}${field.value.slice(end)}`;
    field.focus();
    field.setSelectionRange(start + before.length, start + before.length + selected.length);
    field.dispatchEvent(new Event('input'));
};

editorFields.forEach((field) => {
    const wrap = field.closest('[data-editor-wrap]');
    const preview = wrap?.querySelector('[data-editor-preview]');

    if (!preview) {
        return;
    }

    const toolbar = document.createElement('div');
    toolbar.className = 'admin-editor-toolbar';
    toolbar.innerHTML = `
        <button type="button" data-editor-action="bold">B</button>
        <button type="button" data-editor-action="italic">I</button>
        <button type="button" data-editor-action="heading">H</button>
        <button type="button" data-editor-action="list">•</button>
        <button type="button" data-editor-action="link">Link</button>
    `;
    field.before(toolbar);

    toolbar.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;

        const action = button.dataset.editorAction;
        if (action === 'bold') insertAroundSelection(field, '**');
        if (action === 'italic') insertAroundSelection(field, '*');
        if (action === 'heading') insertAroundSelection(field, '## ', '');
        if (action === 'list') insertAroundSelection(field, '- ', '');
        if (action === 'link') insertAroundSelection(field, '[', '](https://)');
    });

    const update = () => {
        preview.innerHTML = `<p>${renderPreview(field.value || 'Anteprima del testo...')}</p>`;
    };

    field.addEventListener('input', update);
    update();
});

confirmForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

flashMessages.forEach((message) => {
    window.setTimeout(() => {
        message.classList.add('is-hiding');
        window.setTimeout(() => message.remove(), 280);
    }, 3600);
});

updateNavbar();
setActiveSection();

window.addEventListener('scroll', () => {
    updateNavbar();
    setActiveSection();
}, { passive: true });
