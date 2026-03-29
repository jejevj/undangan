
<?php if(isset($guestName) && $guestName): ?>
<section id="pesan" class="section <?php echo e($messageSectionClass ?? ''); ?> reveal">
    <p class="t-upper t-muted">Ucapan & Doa</p>
    <h2 class="section-title">Tinggalkan Pesan</h2>
    <div class="divider"></div>
    <p class="t-muted" style="max-width:480px;margin:0 auto 24px;font-size:.9rem;line-height:1.7">
        Berikan ucapan dan doa restu untuk kedua mempelai
    </p>

    
    <div class="guest-message-form-wrap" style="max-width:520px;margin:0 auto 40px">
        <form id="guestMessageForm" class="guest-message-form">
            <?php echo csrf_field(); ?>
            <div class="form-group mb-3">
                <label for="guestNameInput" class="form-label">Nama Anda</label>
                <input type="text" 
                       id="guestNameInput" 
                       name="guest_name" 
                       class="form-control" 
                       value="<?php echo e($guestName); ?>" 
                       readonly 
                       style="background:#f5f5f5;cursor:not-allowed">
            </div>
            <div class="form-group mb-3">
                <label for="guestMessageInput" class="form-label">Pesan Anda</label>
                <textarea id="guestMessageInput" 
                          name="message" 
                          class="form-control" 
                          rows="4" 
                          maxlength="1000" 
                          placeholder="Tulis ucapan dan doa restu Anda di sini..." 
                          required></textarea>
                <small class="text-muted">Maksimal 1000 karakter</small>
            </div>
            <button type="submit" class="btn-gold" id="submitMessageBtn">
                <span class="btn-text">Kirim Pesan</span>
                <span class="btn-loading" style="display:none">Mengirim...</span>
            </button>
        </form>
    </div>

    
    <?php if($invitation->guestMessages->count() > 0): ?>
    <div class="guest-messages-list" style="max-width:700px;margin:0 auto">
        <h3 style="font-size:1.1rem;margin-bottom:20px;color:var(--brown)">
            Ucapan dari Tamu (<?php echo e($invitation->guestMessages->count()); ?>)
        </h3>
        <div class="messages-container" id="messagesContainer">
            <?php $__currentLoopData = $invitation->guestMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="message-card" data-message-id="<?php echo e($msg->id); ?>">
                <div class="message-header">
                    <div class="message-avatar"><?php echo e($msg->initials); ?></div>
                    <div class="message-meta">
                        <div class="message-name"><?php echo e($msg->formatted_name); ?></div>
                        <div class="message-date"><?php echo e($msg->created_at->diffForHumans()); ?></div>
                    </div>
                </div>
                <div class="message-body"><?php echo e($msg->message); ?></div>
                <div class="message-footer">
                    <button class="btn-like" onclick="likeMessage(<?php echo e($msg->id); ?>, this)" data-likes="<?php echo e($msg->likes_count ?? 0); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        <span class="likes-count"><?php echo e($msg->likes_count ?? 0); ?></span>
                    </button>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<style>
.guest-message-form {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--brown);
    font-size: 0.9rem;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: inherit;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.1);
}

.form-control[readonly] {
    background: #f9fafb;
    cursor: not-allowed;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.btn-loading {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.messages-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: 500px;
    overflow-y: auto;
    padding-right: 8px;
    position: relative;
}

/* Custom scrollbar */
.messages-container::-webkit-scrollbar {
    width: 8px;
}

.messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, var(--gold) 0%, var(--gold-light) 100%);
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: var(--gold);
}

/* Fade effect at top and bottom to indicate scrollable */
.guest-messages-list {
    position: relative;
}

.message-card {
    background: white;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-left: 3px solid var(--gold);
    text-align: left;
}

.message-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.message-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    flex-shrink: 0;
    letter-spacing: 1px;
}

.message-meta {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.message-name {
    font-weight: 600;
    color: var(--brown);
    font-size: 0.95rem;
    text-align: left;
}

.message-date {
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 2px;
    text-align: left;
}

.message-body {
    color: #374151;
    line-height: 1.6;
    font-size: 0.9rem;
    text-align: left;
    margin-left: 60px;
}

.message-footer {
    display: flex;
    align-items: center;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.btn-like {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: white;
    color: #6b7280;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-like:hover {
    border-color: var(--gold);
    color: var(--gold);
    background: rgba(201, 169, 110, 0.05);
}

.btn-like.liked {
    border-color: #ef4444;
    color: #ef4444;
    background: #fef2f2;
}

.btn-like svg {
    width: 16px;
    height: 16px;
}

.btn-like.liked svg {
    fill: #ef4444;
}

.likes-count {
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

@media (max-width: 640px) {
    .guest-message-form {
        padding: 20px;
    }
    
    .message-card {
        padding: 14px;
    }
    
    .message-avatar {
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }
    
    .message-body {
        margin-left: 48px;
    }
    
    .messages-container {
        max-height: 400px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('guestMessageForm');
    const submitBtn = document.getElementById('submitMessageBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const data = {
                guest_name: formData.get('guest_name'),
                message: formData.get('message'),
                _token: formData.get('_token')
            };
            
            // Show loading
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
            
            fetch('<?php echo e(route("invitation.guest-message.store", $invitation->slug)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': data._token
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Show success message with SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        confirmButtonColor: '#C9A96E',
                        confirmButtonText: 'OK'
                    });
                    
                    // Reset form
                    document.getElementById('guestMessageInput').value = '';
                    
                    // Add new message to DOM
                    addMessageToDOM(result.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengirim pesan. Silakan coba lagi.',
                        confirmButtonColor: '#C9A96E',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#C9A96E',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Reset button
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            });
        });
    }
});

// Add new message to DOM
function addMessageToDOM(data) {
    const container = document.getElementById('messagesContainer');
    if (!container) {
        // If container doesn't exist, reload page
        setTimeout(() => window.location.reload(), 1000);
        return;
    }
    
    const likesCount = data.likes_count ?? 0;
    
    const messageHTML = `
        <div class="message-card" data-message-id="${data.id}" style="animation: slideIn 0.3s ease-out">
            <div class="message-header">
                <div class="message-avatar">${data.initials}</div>
                <div class="message-meta">
                    <div class="message-name">${data.guest_name}</div>
                    <div class="message-date">${data.created_at}</div>
                </div>
            </div>
            <div class="message-body">${data.message}</div>
            <div class="message-footer">
                <button class="btn-like" onclick="likeMessage(${data.id}, this)" data-likes="${likesCount}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span class="likes-count">${likesCount}</span>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('afterbegin', messageHTML);
    
    // Scroll to top to show new message
    container.scrollTop = 0;
}

// Like message function
function likeMessage(messageId, button) {
    // Prevent double click
    if (button.classList.contains('liked')) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                      document.querySelector('[name="_token"]').value;
    
    fetch('<?php echo e(route("invitation.guest-message.like", ["slug" => $invitation->slug, "message" => ":id"])); ?>'.replace(':id', messageId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Update likes count
            button.querySelector('.likes-count').textContent = result.likes_count;
            button.classList.add('liked');
            button.disabled = true;
            
            // Store in localStorage to prevent multiple likes
            const likedMessages = JSON.parse(localStorage.getItem('likedMessages') || '[]');
            likedMessages.push(messageId);
            localStorage.setItem('likedMessages', JSON.stringify(likedMessages));
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Check if user already liked messages
document.addEventListener('DOMContentLoaded', function() {
    const likedMessages = JSON.parse(localStorage.getItem('likedMessages') || '[]');
    likedMessages.forEach(messageId => {
        const button = document.querySelector(`[data-message-id="${messageId}"] .btn-like`);
        if (button) {
            button.classList.add('liked');
            button.disabled = true;
        }
    });
});
</script>

<style>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
<?php endif; ?>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitation-templates/_guest_messages.blade.php ENDPATH**/ ?>