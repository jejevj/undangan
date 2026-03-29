/**
 * Live Edit Form Panel
 * Displays all template fields in a collapsible panel
 */

class LiveEditFormPanel {
    constructor(liveEditor) {
        this.liveEditor = liveEditor;
        this.isOpen = false;
        this.fields = [];
        this.panel = null;
    }

    async init() {
        await this.loadFields();
        this.createPanel();
        this.attachToToolbar();
    }

    async loadFields() {
        try {
            const response = await fetch(`/dash/api/invitations/${this.liveEditor.invitationId}/live-edit-fields`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.liveEditor.csrfToken
                }
            });

            const data = await response.json();
            if (data.success) {
                this.fields = data.fields;
            }
        } catch (error) {
            console.error('Error loading fields:', error);
        }
    }

    createPanel() {
        const panel = document.createElement('div');
        panel.id = 'live-edit-form-panel';
        panel.className = 'live-edit-form-panel';
        panel.style.display = 'none';

        let fieldsHTML = '';
        this.fields.forEach(field => {
            fieldsHTML += this.renderField(field);
        });

        panel.innerHTML = `
            <div class="form-panel-header">
                <h3>Edit Semua Field</h3>
                <button class="btn-close-panel" id="closePanelHeaderBtn">×</button>
            </div>
            <div class="form-panel-body">
                ${fieldsHTML}
            </div>
            <div class="form-panel-footer">
                <button class="btn btn-primary" id="saveAllFieldsBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Semua
                </button>
                <button class="btn btn-secondary" id="closeFormPanelBtn">Tutup</button>
            </div>
        `;

        // Add CSS - including all modal styles
        if (!document.getElementById('live-edit-modal-styles')) {
            const style = document.createElement('style');
            style.id = 'live-edit-modal-styles';
            style.textContent = `
                .live-edit-form-panel {
                    position: fixed;
                    top: 80px;
                    right: 20px;
                    width: 400px;
                    max-height: calc(100vh - 100px);
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                    z-index: 999998;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }
                .form-panel-header {
                    padding: 16px 20px;
                    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                    color: white;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .form-panel-header h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                }
                .btn-close-panel {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 28px;
                    line-height: 1;
                    cursor: pointer;
                    padding: 0;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 4px;
                    transition: background 0.2s;
                }
                .btn-close-panel:hover {
                    background: rgba(255, 255, 255, 0.2);
                }
                .form-panel-body {
                    flex: 1;
                    overflow-y: auto;
                    padding: 20px;
                }
                .form-field {
                    margin-bottom: 20px;
                }
                .form-field label {
                    display: block;
                    font-size: 13px;
                    font-weight: 600;
                    color: #374151;
                    margin-bottom: 6px;
                }
                .form-field input,
                .form-field textarea,
                .form-field select {
                    width: 100%;
                    padding: 10px 12px;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    font-size: 14px;
                    transition: border-color 0.2s;
                }
                .form-field input:focus,
                .form-field textarea:focus,
                .form-field select:focus {
                    outline: none;
                    border-color: #3b82f6;
                    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                }
                .form-field textarea {
                    min-height: 80px;
                    resize: vertical;
                }
                .form-panel-footer {
                    padding: 16px 20px;
                    border-top: 1px solid #e5e7eb;
                    display: flex;
                    gap: 10px;
                }
                .form-panel-footer .btn {
                    flex: 1;
                    padding: 10px 16px;
                    border: none;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                    transition: all 0.2s;
                }
                .btn-primary {
                    background: #3b82f6;
                    color: white;
                }
                .btn-primary:hover {
                    background: #2563eb;
                }
                .btn-secondary {
                    background: #e5e7eb;
                    color: #374151;
                }
                .btn-secondary:hover {
                    background: #d1d5db;
                }
                
                /* Modal Styles */
                .live-edit-modal {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    z-index: 999999 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .live-edit-modal-overlay {
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    background: rgba(0, 0, 0, 0.5) !important;
                    z-index: 1 !important;
                }
                .live-edit-modal-content {
                    position: relative !important;
                    background: white !important;
                    border-radius: 12px !important;
                    width: 90% !important;
                    max-width: 800px !important;
                    max-height: 80vh !important;
                    display: flex !important;
                    flex-direction: column !important;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
                    z-index: 2 !important;
                }
                .live-edit-modal-header {
                    padding: 20px 24px !important;
                    border-bottom: 1px solid #e5e7eb !important;
                    display: flex !important;
                    justify-content: space-between !important;
                    align-items: center !important;
                }
                .live-edit-modal-header h3 {
                    margin: 0 !important;
                    font-size: 18px !important;
                    font-weight: 600 !important;
                }
                .btn-close-modal {
                    background: none !important;
                    border: none !important;
                    font-size: 32px !important;
                    line-height: 1 !important;
                    cursor: pointer !important;
                    color: #6b7280 !important;
                    padding: 0 !important;
                    width: 32px !important;
                    height: 32px !important;
                }
                .btn-close-modal:hover {
                    color: #374151 !important;
                }
                .live-edit-modal-body {
                    flex: 1 !important;
                    overflow-y: auto !important;
                    padding: 24px !important;
                }
                .live-edit-modal-footer {
                    padding: 16px 24px !important;
                    border-top: 1px solid #e5e7eb !important;
                    display: flex !important;
                    gap: 12px !important;
                    justify-content: flex-end !important;
                }
                
                /* Photo Grid */
                .photo-grid {
                    display: grid !important;
                    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important;
                    gap: 16px !important;
                }
                .photo-item {
                    position: relative !important;
                    aspect-ratio: 1 !important;
                    border-radius: 8px !important;
                    overflow: hidden !important;
                    cursor: pointer !important;
                    border: 2px solid transparent !important;
                    transition: border-color 0.2s !important;
                }
                .photo-item:hover {
                    border-color: #3b82f6 !important;
                }
                .photo-item img {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                }
                .photo-overlay {
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    background: rgba(0, 0, 0, 0.5) !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    opacity: 0 !important;
                    transition: opacity 0.2s !important;
                }
                .photo-item:hover .photo-overlay {
                    opacity: 1 !important;
                }
                .btn-select-photo {
                    padding: 8px 16px !important;
                    background: #3b82f6 !important;
                    color: white !important;
                    border: none !important;
                    border-radius: 6px !important;
                    cursor: pointer !important;
                    font-size: 14px !important;
                    font-weight: 600 !important;
                }
                .btn-select-photo:hover {
                    background: #2563eb !important;
                }
                
                /* Music List */
                .music-list {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 12px !important;
                }
                .music-item {
                    display: flex !important;
                    justify-content: space-between !important;
                    align-items: center !important;
                    padding: 16px !important;
                    background: #f9fafb !important;
                    border-radius: 8px !important;
                    border: 2px solid transparent !important;
                    transition: all 0.2s !important;
                }
                .music-item:hover {
                    background: #f3f4f6 !important;
                    border-color: #8b5cf6 !important;
                }
                .music-info {
                    flex: 1 !important;
                }
                .music-title {
                    font-weight: 600 !important;
                    color: #111827 !important;
                    margin-bottom: 4px !important;
                }
                .music-artist {
                    font-size: 13px !important;
                    color: #6b7280 !important;
                }
                .btn-select-music {
                    padding: 8px 16px !important;
                    background: #8b5cf6 !important;
                    color: white !important;
                    border: none !important;
                    border-radius: 6px !important;
                    cursor: pointer !important;
                    font-size: 14px !important;
                    font-weight: 600 !important;
                }
                .btn-select-music:hover {
                    background: #7c3aed !important;
                }
                
                /* Gallery Photo Grid */
                .gallery-photo-grid {
                    display: grid !important;
                    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)) !important;
                    gap: 16px !important;
                }
                .gallery-photo-item {
                    position: relative !important;
                    aspect-ratio: 1 !important;
                    border-radius: 8px !important;
                    overflow: hidden !important;
                    cursor: pointer !important;
                    border: 3px solid transparent !important;
                    transition: all 0.3s !important;
                }
                .gallery-photo-item:hover {
                    border-color: #10b981 !important;
                    transform: scale(1.02) !important;
                }
                .gallery-photo-item.selected {
                    border-color: #10b981 !important;
                    box-shadow: 0 0 15px rgba(16, 185, 129, 0.5) !important;
                }
                .gallery-photo-item img {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                }
                .gallery-selection-badge {
                    position: absolute !important;
                    top: 8px !important;
                    right: 8px !important;
                    background: #10b981 !important;
                    color: white !important;
                    border-radius: 50% !important;
                    width: 32px !important;
                    height: 32px !important;
                    display: none !important;
                    align-items: center !important;
                    justify-content: center !important;
                    font-weight: bold !important;
                    font-size: 14px !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
                }
                .gallery-photo-item.selected .gallery-selection-badge {
                    display: flex !important;
                }
                .gallery-photo-overlay {
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    background: rgba(0, 0, 0, 0.6) !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    opacity: 0 !important;
                    transition: opacity 0.2s !important;
                }
                .gallery-photo-item:hover .gallery-photo-overlay {
                    opacity: 1 !important;
                }
                .btn-toggle-gallery-photo {
                    padding: 8px 16px !important;
                    background: #10b981 !important;
                    color: white !important;
                    border: none !important;
                    border-radius: 6px !important;
                    cursor: pointer !important;
                    font-size: 14px !important;
                    font-weight: 600 !important;
                }
                .btn-toggle-gallery-photo:hover {
                    background: #059669 !important;
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(panel);
        this.panel = panel;

        // Attach event listeners
        document.getElementById('saveAllFieldsBtn').addEventListener('click', () => this.saveAll());
        document.getElementById('closeFormPanelBtn').addEventListener('click', () => this.toggle());
        document.getElementById('closePanelHeaderBtn').addEventListener('click', () => this.toggle());

        // Attach photo picker listeners
        this.panel.querySelectorAll('.btn-pick-photo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.openPhotoPicker(fieldKey);
            });
        });

        // Attach photo remove listeners
        this.panel.querySelectorAll('.btn-remove-photo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.removePhoto(fieldKey);
            });
        });

        // Attach music picker listeners
        this.panel.querySelectorAll('.btn-pick-music').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.openMusicPicker(fieldKey);
            });
        });

        // Attach music remove listeners
        this.panel.querySelectorAll('.btn-remove-music').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.removeMusic(fieldKey);
            });
        });
    }

    renderField(field) {
        const value = field.value || '';
        const escapedValue = this.escapeHtml(value);

        let inputHTML = '';
        
        switch (field.type) {
            case 'image':
                const imagePreview = value ? `<img src="${escapedValue}" style="max-width: 100%; max-height: 150px; margin-top: 8px; border-radius: 6px;">` : '';
                inputHTML = `
                    <input type="hidden" data-field-key="${field.key}" class="form-control" value="${escapedValue}">
                    <div style="display: flex; gap: 8px; margin-top: 4px;">
                        <button type="button" class="btn-pick-photo" data-field-key="${field.key}" style="flex: 1; padding: 8px 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            Pilih Foto
                        </button>
                        ${value ? `<button type="button" class="btn-remove-photo" data-field-key="${field.key}" style="padding: 8px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">Hapus</button>` : ''}
                    </div>
                    ${imagePreview}
                `;
                break;
            case 'music':
                const musicPreview = value ? `<div style="margin-top: 8px; padding: 8px; background: #f3f4f6; border-radius: 6px; font-size: 13px;">${escapedValue}</div>` : '';
                inputHTML = `
                    <input type="hidden" data-field-key="${field.key}" class="form-control" value="${escapedValue}">
                    <div style="display: flex; gap: 8px; margin-top: 4px;">
                        <button type="button" class="btn-pick-music" data-field-key="${field.key}" style="flex: 1; padding: 8px 12px; background: #8b5cf6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M9 18V5l12-2v13"/>
                                <circle cx="6" cy="18" r="3"/>
                                <circle cx="18" cy="16" r="3"/>
                            </svg>
                            Pilih Musik
                        </button>
                        ${value ? `<button type="button" class="btn-remove-music" data-field-key="${field.key}" style="padding: 8px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">Hapus</button>` : ''}
                    </div>
                    ${musicPreview}
                `;
                break;
            case 'textarea':
            case 'longtext':
                inputHTML = `<textarea data-field-key="${field.key}" class="form-control">${escapedValue}</textarea>`;
                break;
            case 'date':
                inputHTML = `<input type="date" data-field-key="${field.key}" class="form-control" value="${escapedValue}">`;
                break;
            case 'time':
                inputHTML = `<input type="time" data-field-key="${field.key}" class="form-control" value="${escapedValue}">`;
                break;
            case 'url':
                inputHTML = `<input type="url" data-field-key="${field.key}" class="form-control" value="${escapedValue}">`;
                break;
            case 'select':
                const options = field.options || [];
                let optionsHTML = '<option value="">-- Pilih --</option>';
                options.forEach(opt => {
                    const selected = opt === value ? 'selected' : '';
                    optionsHTML += `<option value="${this.escapeHtml(opt)}" ${selected}>${this.escapeHtml(opt)}</option>`;
                });
                inputHTML = `<select data-field-key="${field.key}" class="form-control">${optionsHTML}</select>`;
                break;
            default:
                inputHTML = `<input type="text" data-field-key="${field.key}" class="form-control" value="${escapedValue}">`;
        }

        return `
            <div class="form-field">
                <label>${this.escapeHtml(field.label)}</label>
                ${inputHTML}
            </div>
        `;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    attachToToolbar() {
        const toolbar = document.getElementById('live-edit-toolbar');
        if (toolbar) {
            const toggleButton = document.createElement('button');
            toggleButton.className = 'btn-live-edit btn-form-panel';
            toggleButton.style.background = '#6366f1';
            toggleButton.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                <span>Edit Form</span>
            `;
            toggleButton.onclick = () => this.toggle();

            // Gallery button
            const galleryButton = document.createElement('button');
            galleryButton.className = 'btn-live-edit btn-gallery';
            galleryButton.style.background = '#10b981';
            galleryButton.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                <span>Edit Gallery</span>
            `;
            galleryButton.onclick = () => this.openGalleryManager();

            // Insert after Live Edit button
            const liveEditBtn = document.getElementById('toggleEditMode');
            liveEditBtn.after(toggleButton);
            toggleButton.after(galleryButton);
        }
    }

    toggle() {
        this.isOpen = !this.isOpen;
        if (this.panel) {
            this.panel.style.display = this.isOpen ? 'flex' : 'none';
        }
    }

    async saveAll() {
        const inputs = this.panel.querySelectorAll('[data-field-key]');
        const updates = [];

        inputs.forEach(input => {
            const fieldKey = input.dataset.fieldKey;
            const value = input.value;
            updates.push({ fieldKey, value });
        });

        // Show saving status
        const status = document.getElementById('editStatus');
        if (status) {
            status.textContent = 'Menyimpan...';
            status.className = 'live-edit-status saving';
        }

        // Save all fields
        for (const update of updates) {
            await this.liveEditor.saveField(update.fieldKey, update.value);
        }

        // Show success
        if (status) {
            status.textContent = 'Tersimpan!';
            status.className = 'live-edit-status saved';
            setTimeout(() => {
                status.textContent = '';
                status.className = 'live-edit-status';
            }, 2000);
        }

        // Reload page to show changes
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    async openPhotoPicker(fieldKey) {
        // Load user photos
        try {
            const response = await fetch(`/dash/api/invitations/${this.liveEditor.invitationId}/live-edit-photos`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.liveEditor.csrfToken
                }
            });

            const data = await response.json();
            if (data.success) {
                this.showPhotoPickerModal(fieldKey, data.photos);
            }
        } catch (error) {
            console.error('Error loading photos:', error);
            alert('Gagal memuat foto. Silakan coba lagi.');
        }
    }

    showPhotoPickerModal(fieldKey, photos) {
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'live-edit-modal';
        modal.innerHTML = `
            <div class="live-edit-modal-overlay"></div>
            <div class="live-edit-modal-content">
                <div class="live-edit-modal-header">
                    <h3>Pilih Foto</h3>
                    <button class="btn-close-modal">×</button>
                </div>
                <div class="live-edit-modal-body">
                    ${photos.length === 0 ? `
                        <div style="text-align: center; padding: 40px; color: #6b7280;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 16px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <p>Belum ada foto di galeri Anda.</p>
                            <a href="/dash/invitations/${this.liveEditor.invitationId}/gallery" class="btn btn-primary" style="display: inline-block; margin-top: 16px; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px;">Upload Foto</a>
                        </div>
                    ` : `
                        <div class="photo-grid">
                            ${photos.map(photo => `
                                <div class="photo-item" data-photo-url="${photo.url}" data-photo-path="${photo.path}">
                                    <img src="${photo.url}" alt="${photo.caption || 'Photo'}">
                                    <div class="photo-overlay">
                                        <button class="btn-select-photo">Pilih</button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `}
                </div>
            </div>
        `;

        // Modal styles already added in createPanel
        document.body.appendChild(modal);

        // Close modal handlers
        modal.querySelector('.btn-close-modal').addEventListener('click', () => {
            modal.remove();
        });
        modal.querySelector('.live-edit-modal-overlay').addEventListener('click', () => {
            modal.remove();
        });

        // Select photo handlers
        modal.querySelectorAll('.btn-select-photo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const photoItem = e.target.closest('.photo-item');
                const photoUrl = photoItem.dataset.photoUrl;
                const photoPath = photoItem.dataset.photoPath;
                
                // Update field value
                const input = this.panel.querySelector(`[data-field-key="${fieldKey}"]`);
                if (input) {
                    input.value = photoPath;
                }
                
                modal.remove();
                
                // Refresh panel to show preview
                this.loadFields().then(() => {
                    this.panel.querySelector('.form-panel-body').innerHTML = '';
                    this.fields.forEach(field => {
                        this.panel.querySelector('.form-panel-body').innerHTML += this.renderField(field);
                    });
                    // Re-attach listeners
                    this.attachFieldListeners();
                });
            });
        });
    }

    async openMusicPicker(fieldKey) {
        // Load user music
        try {
            const response = await fetch(`/dash/api/invitations/${this.liveEditor.invitationId}/live-edit-music`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.liveEditor.csrfToken
                }
            });

            const data = await response.json();
            if (data.success) {
                this.showMusicPickerModal(fieldKey, data.music);
            }
        } catch (error) {
            console.error('Error loading music:', error);
            alert('Gagal memuat musik. Silakan coba lagi.');
        }
    }

    showMusicPickerModal(fieldKey, musicList) {
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'live-edit-modal';
        modal.innerHTML = `
            <div class="live-edit-modal-overlay"></div>
            <div class="live-edit-modal-content">
                <div class="live-edit-modal-header">
                    <h3>Pilih Musik</h3>
                    <button class="btn-close-modal">×</button>
                </div>
                <div class="live-edit-modal-body">
                    ${musicList.length === 0 ? `
                        <div style="text-align: center; padding: 40px; color: #6b7280;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 16px;">
                                <path d="M9 18V5l12-2v13"/>
                                <circle cx="6" cy="18" r="3"/>
                                <circle cx="18" cy="16" r="3"/>
                            </svg>
                            <p>Belum ada musik yang tersedia.</p>
                            <a href="/dash/music" class="btn btn-primary" style="display: inline-block; margin-top: 16px; padding: 10px 20px; background: #8b5cf6; color: white; text-decoration: none; border-radius: 6px;">Lihat Musik</a>
                        </div>
                    ` : `
                        <div class="music-list">
                            ${musicList.map(music => `
                                <div class="music-item" data-music-path="${music.file_path}">
                                    <div class="music-info">
                                        <div class="music-title">${music.title}</div>
                                        <div class="music-artist">${music.artist || 'Unknown Artist'}</div>
                                    </div>
                                    <button class="btn-select-music">Pilih</button>
                                </div>
                            `).join('')}
                        </div>
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
                            <a href="/dash/music/upload" class="btn btn-secondary" style="display: inline-block; padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px;">Upload Musik Baru</a>
                        </div>
                    `}
                </div>
            </div>
        `;

        // Modal styles already added in createPanel
        document.body.appendChild(modal);

        // Close modal handlers
        modal.querySelector('.btn-close-modal').addEventListener('click', () => {
            modal.remove();
        });
        modal.querySelector('.live-edit-modal-overlay').addEventListener('click', () => {
            modal.remove();
        });

        // Select music handlers
        modal.querySelectorAll('.btn-select-music').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const musicItem = e.target.closest('.music-item');
                const musicPath = musicItem.dataset.musicPath;
                
                // Update field value
                const input = this.panel.querySelector(`[data-field-key="${fieldKey}"]`);
                if (input) {
                    input.value = musicPath;
                }
                
                modal.remove();
                
                // Refresh panel to show preview
                this.loadFields().then(() => {
                    this.panel.querySelector('.form-panel-body').innerHTML = '';
                    this.fields.forEach(field => {
                        this.panel.querySelector('.form-panel-body').innerHTML += this.renderField(field);
                    });
                    // Re-attach listeners
                    this.attachFieldListeners();
                });
            });
        });
    }

    removePhoto(fieldKey) {
        const input = this.panel.querySelector(`[data-field-key="${fieldKey}"]`);
        if (input) {
            input.value = '';
        }
        
        // Refresh panel
        this.loadFields().then(() => {
            this.panel.querySelector('.form-panel-body').innerHTML = '';
            this.fields.forEach(field => {
                this.panel.querySelector('.form-panel-body').innerHTML += this.renderField(field);
            });
            this.attachFieldListeners();
        });
    }

    removeMusic(fieldKey) {
        const input = this.panel.querySelector(`[data-field-key="${fieldKey}"]`);
        if (input) {
            input.value = '';
        }
        
        // Refresh panel
        this.loadFields().then(() => {
            this.panel.querySelector('.form-panel-body').innerHTML = '';
            this.fields.forEach(field => {
                this.panel.querySelector('.form-panel-body').innerHTML += this.renderField(field);
            });
            this.attachFieldListeners();
        });
    }

    attachFieldListeners() {
        // Attach photo picker listeners
        this.panel.querySelectorAll('.btn-pick-photo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.openPhotoPicker(fieldKey);
            });
        });

        // Attach photo remove listeners
        this.panel.querySelectorAll('.btn-remove-photo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.removePhoto(fieldKey);
            });
        });

        // Attach music picker listeners
        this.panel.querySelectorAll('.btn-pick-music').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.openMusicPicker(fieldKey);
            });
        });

        // Attach music remove listeners
        this.panel.querySelectorAll('.btn-remove-music').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fieldKey = e.currentTarget.dataset.fieldKey;
                this.removeMusic(fieldKey);
            });
        });
    }

    async openGalleryManager() {
        // Load gallery data
        try {
            const response = await fetch(`/dash/api/invitations/${this.liveEditor.invitationId}/live-edit-gallery`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.liveEditor.csrfToken
                }
            });

            const data = await response.json();
            if (data.success) {
                this.showGalleryManagerModal(data.allPhotos, data.selectedPhotoIds);
            }
        } catch (error) {
            console.error('Error loading gallery:', error);
            alert('Gagal memuat gallery. Silakan coba lagi.');
        }
    }

    showGalleryManagerModal(allPhotos, selectedPhotoIds) {
        let selectedPhotos = [...selectedPhotoIds];

        // Create modal
        const modal = document.createElement('div');
        modal.className = 'live-edit-modal live-edit-gallery-modal';
        modal.innerHTML = `
            <div class="live-edit-modal-overlay"></div>
            <div class="live-edit-modal-content" style="max-width: 900px;">
                <div class="live-edit-modal-header">
                    <h3>Edit Gallery Undangan</h3>
                    <button class="btn-close-modal">×</button>
                </div>
                <div class="live-edit-modal-body">
                    ${allPhotos.length === 0 ? `
                        <div style="text-align: center; padding: 40px; color: #6b7280;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 16px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <p>Belum ada foto di gallery pool Anda.</p>
                            <a href="/dash/invitations/${this.liveEditor.invitationId}/gallery" class="btn btn-primary" style="display: inline-block; margin-top: 16px; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 6px;">Upload Foto</a>
                        </div>
                    ` : `
                        <div style="margin-bottom: 16px; padding: 12px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; color: #166534;">
                            <strong>Cara Kerja:</strong> Klik foto untuk memilih. Urutan klik = urutan tampil di undangan. 
                            <span id="gallery-selected-count" style="display: inline-block; margin-left: 12px; padding: 4px 12px; background: #10b981; color: white; border-radius: 12px; font-weight: 600;">
                                ${selectedPhotos.length} dipilih
                            </span>
                        </div>
                        <div class="gallery-photo-grid" id="galleryPhotoGrid">
                            ${allPhotos.map(photo => {
                                const isSelected = selectedPhotos.includes(photo.id);
                                const order = isSelected ? selectedPhotos.indexOf(photo.id) + 1 : '';
                                return `
                                    <div class="gallery-photo-item ${isSelected ? 'selected' : ''}" 
                                         data-photo-id="${photo.id}"
                                         data-selected="${isSelected}">
                                        <img src="${photo.url}" alt="${photo.caption || 'Photo'}">
                                        <div class="gallery-selection-badge">${order}</div>
                                        <div class="gallery-photo-overlay">
                                            <button class="btn-toggle-gallery-photo">${isSelected ? 'Hapus' : 'Pilih'}</button>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    `}
                </div>
                <div class="live-edit-modal-footer" style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end;">
                    <button class="btn btn-secondary" id="cancelGalleryBtn" style="padding: 10px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                    <button class="btn btn-primary" id="saveGalleryBtn" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Simpan Gallery
                    </button>
                </div>
            </div>
        `;

        // Modal styles already added in createPanel
        document.body.appendChild(modal);

        // Photo selection handlers
        modal.querySelectorAll('.gallery-photo-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (e.target.closest('.btn-toggle-gallery-photo')) return;

                const photoId = parseInt(this.dataset.photoId);
                const isSelected = this.dataset.selected === 'true';

                if (isSelected) {
                    // Deselect
                    const index = selectedPhotos.indexOf(photoId);
                    if (index > -1) {
                        selectedPhotos.splice(index, 1);
                    }
                    this.classList.remove('selected');
                    this.dataset.selected = 'false';
                    this.querySelector('.btn-toggle-gallery-photo').textContent = 'Pilih';
                } else {
                    // Select
                    selectedPhotos.push(photoId);
                    this.classList.add('selected');
                    this.dataset.selected = 'true';
                    this.querySelector('.btn-toggle-gallery-photo').textContent = 'Hapus';
                }

                // Update badges
                modal.querySelectorAll('.gallery-photo-item').forEach(item => {
                    const photoId = parseInt(item.dataset.photoId);
                    const badge = item.querySelector('.gallery-selection-badge');
                    const index = selectedPhotos.indexOf(photoId);
                    
                    if (index > -1) {
                        badge.textContent = index + 1;
                    }
                });

                // Update count
                const countBadge = modal.querySelector('#gallery-selected-count');
                if (countBadge) {
                    countBadge.textContent = selectedPhotos.length + ' dipilih';
                }
            });
        });

        // Close modal handlers
        modal.querySelector('.btn-close-modal').addEventListener('click', () => {
            modal.remove();
        });
        modal.querySelector('.live-edit-modal-overlay').addEventListener('click', () => {
            modal.remove();
        });
        modal.querySelector('#cancelGalleryBtn').addEventListener('click', () => {
            modal.remove();
        });

        // Save gallery selection
        modal.querySelector('#saveGalleryBtn').addEventListener('click', async () => {
            try {
                const response = await fetch(`/dash/api/invitations/${this.liveEditor.invitationId}/live-edit-gallery`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.liveEditor.csrfToken
                    },
                    body: JSON.stringify({
                        photo_ids: selectedPhotos
                    })
                });

                const data = await response.json();
                if (data.success) {
                    // Show success message
                    const status = document.getElementById('editStatus');
                    if (status) {
                        status.textContent = 'Gallery tersimpan!';
                        status.className = 'live-edit-status saved';
                        setTimeout(() => {
                            status.textContent = '';
                            status.className = 'live-edit-status';
                        }, 2000);
                    }

                    modal.remove();

                    // Reload page to show changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Gagal menyimpan gallery: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving gallery:', error);
                alert('Terjadi kesalahan saat menyimpan gallery.');
            }
        });
    }
}

// Global instance
let liveEditFormPanel = null;
