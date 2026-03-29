/**
 * Live Edit System for Invitation Templates
 * Version: 1.0.0
 */

class LiveEditor {
    constructor(invitationId) {
        console.log('LiveEditor constructor called with ID:', invitationId);
        this.invitationId = invitationId;
        this.isEditMode = false;
        this.editableElements = [];
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        console.log('LiveEditor CSRF token:', this.csrfToken);
        this.saveTimeout = null;
        this.init();
    }

    init() {
        console.log('LiveEditor init() called');
        this.createToolbar();
        this.setupEventListeners();
        console.log('LiveEditor toolbar created and listeners setup');
    }

    createToolbar() {
        console.log('Creating toolbar...');
        
        // Check if toolbar already exists
        if (document.getElementById('live-edit-toolbar')) {
            console.log('Toolbar already exists, removing old one');
            document.getElementById('live-edit-toolbar').remove();
        }
        
        const toolbar = document.createElement('div');
        toolbar.id = 'live-edit-toolbar';
        console.log('Toolbar element created');
        toolbar.innerHTML = `
            <div class="live-edit-toolbar-inner">
                <button id="toggleEditMode" class="btn-live-edit" title="Toggle Edit Mode">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    <span>Live Edit</span>
                </button>
                <button id="editGallery" class="btn-live-edit btn-gallery" title="Edit Gallery" style="background: #8b5cf6;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span>Gallery</span>
                </button>
                <button id="saveChanges" class="btn-live-edit btn-save" style="display:none;" title="Save Changes">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Simpan</span>
                </button>
                <div class="live-edit-status" id="editStatus"></div>
            </div>
        `;

        // Add CSS
        const style = document.createElement('style');
        style.textContent = `
            #live-edit-toolbar {
                position: fixed !important;
                top: 20px !important;
                right: 20px !important;
                z-index: 999999 !important;
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px);
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                padding: 12px 16px;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            .live-edit-toolbar-inner {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .btn-live-edit {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }
            .btn-live-edit:hover {
                background: #2563eb;
                transform: translateY(-1px);
            }
            .btn-live-edit.active {
                background: #10b981;
            }
            .btn-save {
                background: #f59e0b;
            }
            .btn-save:hover {
                background: #d97706;
            }
            .btn-gallery {
                background: #8b5cf6 !important;
            }
            .btn-gallery:hover {
                background: #7c3aed !important;
            }
            .live-edit-status {
                font-size: 12px;
                color: #6b7280;
                padding: 0 8px;
            }
            .live-edit-status.saving {
                color: #f59e0b;
            }
            .live-edit-status.saved {
                color: #10b981;
            }
            [data-editable] {
                position: relative;
                transition: all 0.2s;
            }
            [data-editable].edit-mode {
                outline: 2px dashed #3b82f6 !important;
                outline-offset: 4px !important;
                cursor: text !important;
                min-height: 20px !important;
                background: rgba(59, 130, 246, 0.05) !important;
            }
            [data-editable].edit-mode:hover {
                outline-color: #10b981;
                background: rgba(59, 130, 246, 0.05);
            }
            [data-editable].edit-mode:focus {
                outline: 2px solid #10b981;
                background: rgba(16, 185, 129, 0.05);
            }
            [data-editable].edit-mode::before {
                content: attr(data-field-label);
                position: absolute;
                top: -24px;
                left: 0;
                font-size: 11px;
                font-weight: 600;
                color: #3b82f6;
                background: white;
                padding: 2px 8px;
                border-radius: 4px;
                opacity: 0;
                transition: opacity 0.2s;
                pointer-events: none;
                white-space: nowrap;
            }
            [data-editable].edit-mode:hover::before {
                opacity: 1;
            }
            @media (max-width: 768px) {
                #live-edit-toolbar {
                    top: 10px;
                    right: 10px;
                    left: 10px;
                }
                .live-edit-toolbar-inner {
                    justify-content: space-between;
                }
                .btn-live-edit span {
                    display: none;
                }
            }
        `;
        document.head.appendChild(style);
        console.log('Style appended to head');
        
        // Ensure body is ready
        if (document.body) {
            document.body.appendChild(toolbar);
            console.log('Toolbar appended to body');
            console.log('Toolbar element:', document.getElementById('live-edit-toolbar'));
            console.log('Toolbar computed style:', window.getComputedStyle(toolbar).display);
            console.log('Toolbar position:', window.getComputedStyle(toolbar).position);
            console.log('Toolbar z-index:', window.getComputedStyle(toolbar).zIndex);
        } else {
            console.error('Body not ready!');
            setTimeout(() => {
                document.body.appendChild(toolbar);
                console.log('Toolbar appended to body (delayed)');
            }, 100);
        }
    }

    setupEventListeners() {
        document.getElementById('toggleEditMode').addEventListener('click', () => {
            this.toggleEditMode();
        });

        document.getElementById('editGallery').addEventListener('click', () => {
            // Redirect to gallery management page
            window.location.href = `/dash/invitations/${this.invitationId}/gallery`;
        });

        document.getElementById('saveChanges').addEventListener('click', () => {
            this.showStatus('Semua perubahan tersimpan', 'saved');
        });
    }

    toggleEditMode() {
        this.isEditMode = !this.isEditMode;
        const btn = document.getElementById('toggleEditMode');
        const saveBtn = document.getElementById('saveChanges');

        if (this.isEditMode) {
            btn.classList.add('active');
            btn.querySelector('span').textContent = 'Mode Edit Aktif';
            saveBtn.style.display = 'flex';
            this.enableEditing();
        } else {
            btn.classList.remove('active');
            btn.querySelector('span').textContent = 'Live Edit';
            saveBtn.style.display = 'none';
            this.disableEditing();
        }
    }

    enableEditing() {
        console.log('Enabling edit mode...');
        // Find all editable elements
        this.editableElements = document.querySelectorAll('[data-editable]');
        console.log('Found editable elements:', this.editableElements.length);
        
        this.editableElements.forEach((el, index) => {
            console.log(`Element ${index}:`, el, 'Type:', el.getAttribute('data-field-type'));
            el.classList.add('edit-mode');
            const fieldType = el.getAttribute('data-field-type');

            if (fieldType === 'text' || fieldType === 'textarea') {
                el.contentEditable = true;
                console.log(`Made element ${index} contentEditable`);
                
                // Prevent other event handlers
                el.addEventListener('click', (e) => {
                    e.stopPropagation();
                    console.log('Element clicked, focusing...');
                    el.focus();
                }, true);
                
                el.addEventListener('input', (e) => this.handleTextChange(e));
                el.addEventListener('blur', (e) => this.handleBlur(e));
            } else if (fieldType === 'image') {
                this.makeImageEditable(el);
            } else if (fieldType === 'date' || fieldType === 'time') {
                this.makeDateTimeEditable(el);
            }
        });

        this.showStatus('Mode edit aktif - Klik pada teks untuk mengedit', 'saved');
    }

    disableEditing() {
        this.editableElements.forEach(el => {
            el.classList.remove('edit-mode');
            el.contentEditable = false;
            el.removeEventListener('input', this.handleTextChange);
            el.removeEventListener('blur', this.handleBlur);
        });
        this.showStatus('');
    }

    handleTextChange(e) {
        const element = e.target;
        const fieldKey = element.getAttribute('data-field-key');
        
        // Debounce auto-save
        clearTimeout(this.saveTimeout);
        this.showStatus('Menyimpan...', 'saving');
        
        this.saveTimeout = setTimeout(() => {
            this.saveField(fieldKey, element.textContent);
        }, 1000);
    }

    handleBlur(e) {
        const element = e.target;
        const fieldKey = element.getAttribute('data-field-key');
        
        // Save immediately on blur
        clearTimeout(this.saveTimeout);
        this.saveField(fieldKey, element.textContent);
    }

    makeImageEditable(el) {
        const fieldKey = el.getAttribute('data-field-key');
        
        // Create upload button overlay
        const overlay = document.createElement('div');
        overlay.className = 'image-edit-overlay';
        overlay.innerHTML = `
            <button class="btn-change-image">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Ganti Foto
            </button>
            <input type="file" accept="image/*" style="display:none;" class="image-upload-input">
        `;

        const style = document.createElement('style');
        style.textContent = `
            .image-edit-overlay {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.6);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.2s;
            }
            [data-editable].edit-mode:hover .image-edit-overlay {
                opacity: 1;
            }
            .btn-change-image {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 10px 20px;
                background: white;
                color: #1f2937;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            }
        `;
        document.head.appendChild(style);

        el.style.position = 'relative';
        el.appendChild(overlay);

        const btn = overlay.querySelector('.btn-change-image');
        const input = overlay.querySelector('.image-upload-input');

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            input.click();
        });

        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.uploadImage(fieldKey, file, el);
            }
        });
    }

    makeDateTimeEditable(el) {
        el.addEventListener('click', () => {
            const fieldKey = el.getAttribute('data-field-key');
            const fieldType = el.getAttribute('data-field-type');
            const currentValue = el.textContent.trim();

            const input = document.createElement('input');
            input.type = fieldType;
            input.value = currentValue;
            input.style.cssText = window.getComputedStyle(el).cssText;
            
            el.replaceWith(input);
            input.focus();

            input.addEventListener('change', () => {
                this.saveField(fieldKey, input.value);
                const newEl = document.createElement(el.tagName);
                newEl.className = el.className;
                newEl.setAttribute('data-editable', '');
                newEl.setAttribute('data-field-key', fieldKey);
                newEl.setAttribute('data-field-type', fieldType);
                newEl.textContent = input.value;
                input.replaceWith(newEl);
                this.makeDateTimeEditable(newEl);
            });

            input.addEventListener('blur', () => {
                const newEl = document.createElement(el.tagName);
                newEl.className = el.className;
                newEl.setAttribute('data-editable', '');
                newEl.setAttribute('data-field-key', fieldKey);
                newEl.setAttribute('data-field-type', fieldType);
                newEl.textContent = currentValue;
                input.replaceWith(newEl);
                this.makeDateTimeEditable(newEl);
            });
        });
    }

    async saveField(fieldKey, value) {
        console.log('Saving field:', fieldKey, 'Value:', value);
        try {
            const response = await fetch(`/dash/api/invitations/${this.invitationId}/live-edit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    field_key: fieldKey,
                    value: value,
                }),
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                this.showStatus('Tersimpan', 'saved');
                setTimeout(() => this.showStatus(''), 2000);
            } else {
                this.showStatus('Gagal menyimpan: ' + (data.error || data.message || 'Unknown error'), 'error');
                console.error('Save failed:', data);
            }
        } catch (error) {
            console.error('Save error:', error);
            this.showStatus('Error: ' + error.message, 'error');
        }
    }

    async uploadImage(fieldKey, file, imgElement) {
        this.showStatus('Mengupload gambar...', 'saving');

        const formData = new FormData();
        formData.append('field_key', fieldKey);
        formData.append('value', file);

        try {
            const response = await fetch(`/dash/api/invitations/${this.invitationId}/live-edit`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                // Update image src
                const img = imgElement.querySelector('img');
                if (img) {
                    img.src = `/storage/${data.value}`;
                }
                this.showStatus('Gambar berhasil diupload', 'saved');
                setTimeout(() => this.showStatus(''), 2000);
            } else {
                this.showStatus('Gagal upload gambar', 'error');
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showStatus('Error: ' + error.message, 'error');
        }
    }

    showStatus(message, type = '') {
        const status = document.getElementById('editStatus');
        status.textContent = message;
        status.className = 'live-edit-status ' + type;
    }
}

// Auto-initialize if invitation ID is present
document.addEventListener('DOMContentLoaded', () => {
    console.log('Live Edit: DOMContentLoaded fired');
    
    const invitationId = document.body.getAttribute('data-invitation-id');
    const isOwner = document.body.getAttribute('data-is-owner') === 'true';
    
    console.log('Live Edit: Invitation ID =', invitationId);
    console.log('Live Edit: Is Owner =', isOwner);
    
    if (invitationId && isOwner) {
        console.log('Live Edit: Initializing...');
        
        // Add CSRF token if not present
        if (!document.querySelector('meta[name="csrf-token"]')) {
            console.warn('Live Edit: CSRF token not found, creating one');
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = document.querySelector('meta[name="csrf-token"]')?.content || '';
            document.head.appendChild(meta);
        } else {
            console.log('Live Edit: CSRF token found');
        }
        
        window.liveEditor = new LiveEditor(invitationId);
        console.log('Live Edit: Initialized successfully');
        
        // Initialize form panel
        if (typeof LiveEditFormPanel !== 'undefined') {
            window.liveEditFormPanel = new LiveEditFormPanel(window.liveEditor);
            window.liveEditFormPanel.init();
            console.log('Live Edit: Form panel initialized');
        }
    } else {
        console.log('Live Edit: Not initializing (missing ID or not owner)');
    }
});
